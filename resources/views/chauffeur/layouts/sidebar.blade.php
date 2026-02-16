<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: #ffeaca">
    <div class="mdc-drawer__header">
        <a href="{{ route('chauffeur.dashboard') }}" class="brand-logo">
            @auth('chauffeur')
                @if (Auth::guard('chauffeur')->user()->profile_image) <!-- Note: Old template used profile_image, Caisse used profile_picture. I'll stick to profile_image based on PersonnelController -->
                    <img src="{{ asset('storage/' . Auth::guard('chauffeur')->user()->profile_image) }}"
                        style="width: 50%; margin-left: 50px; border-radius: 50%; object-fit: cover;" alt="Photo de profil">
                @else
                    <div style="width: 50%; margin-left: 50px; display: flex; align-items: center; justify-content: center;">
                        <div
                            style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(to right, #e94e1a, #d33d0f); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; font-weight: bold;">
                            {{ strtoupper(substr(Auth::guard('chauffeur')->user()->prenom, 0, 1)) }}{{ strtoupper(substr(Auth::guard('chauffeur')->user()->name, 0, 1)) }}
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
            <p class="name text-center text-black">{{ Auth::guard('chauffeur')->user()->name }}
                {{ Auth::guard('chauffeur')->user()->prenom }}</p>
            <p class="email text-center text-black">{{ Auth::guard('chauffeur')->user()->email }}</p>
        </div>
        <div class="mdc-list-group">
            <nav class="mdc-list mdc-drawer-menu">
                <!-- Tableau de bord -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('chauffeur.dashboard') }}">
                        <i class="fas fa-home mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Tableau de bord
                    </a>
                </div>

                <!-- S'assigner un voyage (Programmes) -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('chauffeur.programmes') }}">
                        <i class="fas fa-calendar-check mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        S'assigner un voyage
                    </a>
                </div>

                <!-- Mes Voyages -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('chauffeur.voyages') }}">
                        <i class="fas fa-road mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Mes Voyages
                    </a>
                </div>
            </nav>
        </div>
    </div>
</aside>
