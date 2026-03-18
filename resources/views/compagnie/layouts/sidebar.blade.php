<aside class="sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route('compagnie.dashboard') }}" class="sidebar-brand-link">
            @if(Auth::guard('compagnie')->user()->path_logo)
                <img src="{{ asset('storage/' . Auth::guard('compagnie')->user()->path_logo) }}"
                     alt="logo" class="sidebar-logo-img">
            @else
                <div class="sidebar-logo-fallback">
                    {{ strtoupper(substr(Auth::guard('compagnie')->user()->name, 0, 2)) }}
                </div>
            @endif
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-name">{{ Auth::guard('compagnie')->user()->sigle }}</span>
                <span class="sidebar-brand-role">Espace Compagnie</span>
            </div>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav" id="sidebarNav">

        {{-- Principal --}}
        <div class="nav-section-label">Principal</div>

        <a class="nav-item {{ request()->routeIs('compagnie.dashboard') ? 'active' : '' }}"
           href="{{ route('compagnie.dashboard') }}">
            <i class="nav-icon fas fa-home"></i>
            Tableau de bord
        </a>

        <a class="nav-item" href="{{ route('compagnie.tracking.index') }}">
            <i class="nav-icon fas fa-map-marked-alt" style="color:#10B981"></i>
            Voyages en cours
            <span class="nav-live-dot"></span>
        </a>

        @php
            $unreadGareMessages = Auth::guard('compagnie')->user()
                ->receivedGareMessages()->where('is_read', false)->count();
        @endphp
        <a class="nav-item {{ request()->routeIs('compagnie.messages.*') ? 'active' : '' }}"
           href="{{ route('compagnie.messages.index') }}">
            <i class="nav-icon fas fa-envelope"></i>
            Messages
            @if($unreadGareMessages > 0)
                <span class="nav-badge">{{ $unreadGareMessages }}</span>
            @endif
        </a>

        {{-- Gestion --}}
        <div class="nav-section-label">Gestion</div>

        {{-- Réservations --}}
        <div class="nav-item nav-has-sub {{ request()->routeIs('company.reservation.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-ticket-alt"></i>
            Réservations
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('company.reservation.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item {{ request()->get('tab') === 'en-cours' ? 'sub-active' : '' }}"
                   href="{{ route('company.reservation.index', ['tab' => 'en-cours']) }}">En cours</a>
                <a class="nav-sub-item {{ request()->get('tab') === 'terminees' ? 'sub-active' : '' }}"
                   href="{{ route('company.reservation.index', ['tab' => 'terminees']) }}">Terminées</a>
                <a class="nav-sub-item {{ request()->routeIs('company.reservation.details') ? 'sub-active' : '' }}"
                   href="{{ route('company.reservation.details') }}">Détails &amp; Stats</a>
            </div>
        </div>

        {{-- Programmation --}}
        <div class="nav-item nav-has-sub {{ request()->routeIs('programme.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-boxes"></i>
            Programmation
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('programme.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item" href="{{ route('programme.create') }}">Ajouter</a>
                <a class="nav-sub-item" href="{{ route('programme.index') }}">Listes</a>
                <a class="nav-sub-item" href="{{ route('programme.history') }}">Historiques</a>
            </div>
        </div>

        {{-- Agents --}}
        <div class="nav-item nav-has-sub {{ request()->routeIs('compagnie.agents.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-users"></i>
            Agents
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('compagnie.agents.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item" href="{{ route('compagnie.agents.create') }}">Ajouter</a>
                <a class="nav-sub-item" href="{{ route('compagnie.agents.index') }}">Liste</a>
            </div>
        </div>

        {{-- Personnel --}}
        <div class="nav-item nav-has-sub {{ request()->routeIs('personnel.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-user-tie"></i>
            Personnel
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('personnel.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item" href="{{ route('personnel.create') }}">Ajouter</a>
                <a class="nav-sub-item" href="{{ route('personnel.index') }}">Liste</a>
            </div>
        </div>

        {{-- Caisse --}}
        <!-- <div class="nav-item nav-has-sub {{ request()->routeIs('compagnie.caisse.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-cash-register"></i>
            Caisse
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('compagnie.caisse.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item" href="{{ route('compagnie.caisse.create') }}">Ajouter</a>
                <a class="nav-sub-item" href="{{ route('compagnie.caisse.index') }}">Liste</a>
            </div>
        </div> -->

        {{-- Ressources --}}
        <div class="nav-section-label">Ressources</div>

        {{-- Véhicules --}}
        <div class="nav-item nav-has-sub {{ request()->routeIs('vehicule.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-bus"></i>
            Véhicules
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('vehicule.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item" href="{{ route('vehicule.create') }}">Ajouter</a>
                <a class="nav-sub-item" href="{{ route('vehicule.index') }}">Liste</a>
            </div>
        </div>

        {{-- Itinéraire --}}
        <div class="nav-item nav-has-sub {{ request()->routeIs('itineraire.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-route"></i>
            Itinéraire
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('itineraire.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item" href="{{ route('itineraire.create') }}">Ajouter</a>
                <a class="nav-sub-item" href="{{ route('itineraire.index') }}">Liste</a>
            </div>
        </div>

        {{-- Gares --}}
        <div class="nav-item nav-has-sub {{ request()->routeIs('gare.*') ? 'sub-open' : '' }}"
             onclick="toggleNavSub(this)">
            <i class="nav-icon fas fa-building"></i>
            Gares
            <i class="nav-chevron fas fa-chevron-right"></i>
        </div>
        <div class="nav-sub-wrap {{ request()->routeIs('gare.*') ? 'open' : '' }}">
            <div class="nav-sub">
                <a class="nav-sub-item" href="{{ route('gare.create') }}">Ajouter</a>
                <a class="nav-sub-item" href="{{ route('gare.index') }}">Liste</a>
            </div>
        </div>

        {{-- Alertes --}}
        <div class="nav-section-label">Alertes</div>

        @php
            $newSignalements = \App\Models\Signalement::whereHas('programme', function ($q) {
                $q->where('compagnie_id', Auth::guard('compagnie')->id());
            })->where('is_read_by_company', false)->count();
        @endphp
        <a class="nav-item nav-item-danger {{ request()->routeIs('compagnie.signalements.*') ? 'active-danger' : '' }}"
           href="{{ route('compagnie.signalements.index') }}">
            <i class="nav-icon fas fa-exclamation-triangle"></i>
            Signalements
            @if($newSignalements > 0)
                <span class="nav-badge">{{ $newSignalements }}</span>
            @endif
        </a>

    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <a class="nav-item {{ request()->routeIs('compagnie.profile') ? 'active' : '' }}" href="{{ route('compagnie.profile') }}">
            <i class="nav-icon fas fa-id-card"></i>
            Mon Profil
        </a>
    </div>

</aside>

{{-- Backdrop mobile --}}
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>