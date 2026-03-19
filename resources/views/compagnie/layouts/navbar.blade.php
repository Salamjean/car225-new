<header class="topbar">

    {{-- Burger mobile --}}
    <button class="topbar-burger" onclick="openSidebar()" aria-label="Menu">
        <i class="fas fa-bars"></i>
    </button>

    {{-- Titre page --}}
    <div class="topbar-title-wrap">
        <h1 class="topbar-title">@yield('page-title', 'Tableau de bord')</h1>
        <span class="topbar-breadcrumb">@yield('page-subtitle', 'Vue d\'ensemble de votre activité')</span>
    </div>

    {{-- Actions droite --}}
    <div class="topbar-actions">

        {{-- Solde compagnie --}}
        <div class="topbar-solde">
            <i class="fas fa-coins topbar-solde-icon"></i>
            <div class="topbar-solde-info">
                <span class="topbar-solde-label">Solde</span>
                <span class="topbar-solde-val">
                    {{ number_format(Auth::guard('compagnie')->user()->tickets, 0, ',', ' ') }}
                    <span class="topbar-solde-unit">CFA</span>
                </span>
            </div>
        </div>

        {{-- Notifications --}}
        @php
            $notifCount = \App\Models\Signalement::whereHas('programme', function ($q) {
                $q->where('compagnie_id', Auth::guard('compagnie')->id());
            })->where('is_read_by_company', false)->count();
        @endphp
        <a href="{{ route('compagnie.signalements.index') }}" class="topbar-icon-btn" title="Signalements">
            <i class="fas fa-bell"></i>
            @if($notifCount > 0)
                <span class="topbar-notif-dot"></span>
            @endif
        </a>

        {{-- Actualiser --}}
        <button class="topbar-icon-btn" onclick="window.location.reload()" title="Actualiser">
            <i class="fas fa-sync-alt"></i>
        </button>

        {{-- Profil dropdown --}}
        <div class="topbar-profile-wrap" id="topbarProfileWrap">
            <button class="topbar-profile-btn" onclick="toggleProfileMenu()" id="topbarProfileBtn">
                @if(Auth::guard('compagnie')->user()->path_logo)
                    <img src="{{ asset('storage/' . Auth::guard('compagnie')->user()->path_logo) }}"
                         alt="logo" class="topbar-profile-img">
                @else
                    <div class="topbar-profile-avatar">
                        {{ strtoupper(substr(Auth::guard('compagnie')->user()->name, 0, 2)) }}
                    </div>
                @endif
                <span class="topbar-profile-name d-none d-md-inline">
                    {{ Auth::guard('compagnie')->user()->sigle }}
                </span>
                <i class="fas fa-chevron-down topbar-profile-chevron d-none d-md-inline"></i>
            </button>

            <div class="topbar-dropdown" id="topbarDropdown">
                <div class="topbar-dropdown-header">
                    <div class="topbar-dropdown-name">{{ Auth::guard('compagnie')->user()->name }}</div>
                    <div class="topbar-dropdown-email">{{ Auth::guard('compagnie')->user()->email }}</div>
                </div>
                <div class="topbar-dropdown-divider"></div>
                <a class="topbar-dropdown-item" href="{{ route('compagnie.profile') }}">
                    <i class="fas fa-id-card"></i> Mon Profil
                </a>
                <div class="topbar-dropdown-divider"></div>
                <a class="topbar-dropdown-item topbar-dropdown-logout" href="{{ route('compagnie.logout') }}">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            </div>
        </div>

    </div>
</header>