@php
    $currentRoute = Route::currentRouteName() ?? '';
    $user = Auth::guard('chauffeur')->user();
    $initials = strtoupper(substr($user->prenom ?? '', 0, 1)) . strtoupper(substr($user->name ?? '', 0, 1));

    $unreadCompany = $user->messages()->where('is_read', false)->count();
    $unreadGare = $user->receivedGareMessages()->where('is_read', false)->count();
    $totalUnread = $unreadCompany + $unreadGare;
@endphp

<aside id="chauffeurSidebar" class="chauffeur-sidebar">
    {{-- Logo + Close --}}
    <div class="sidebar-header">
        <a href="{{ route('chauffeur.dashboard') }}" class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-bus"></i>
            </div>
            <span class="brand-text">CAR<span style="color: #f97316;">225</span></span>
        </a>
        <button class="sidebar-close-btn d-md-none" onclick="toggleSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- User profile --}}
    <div class="sidebar-profile">
        <div class="profile-avatar">
            @if($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Photo">
            @else
                <span>{{ $initials }}</span>
            @endif
        </div>
        <div class="profile-info">
            <p class="profile-name">{{ $user->prenom }} {{ $user->name }}</p>
            <p class="profile-role"><i class="fas fa-circle" style="font-size: 6px; color: #22c55e;"></i> Chauffeur</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="sidebar-nav">
        <p class="nav-section-title">Navigation</p>

        <a href="{{ route('chauffeur.dashboard') }}" class="nav-link {{ $currentRoute === 'chauffeur.dashboard' ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-th-large"></i></div>
            <span>Tableau de bord</span>
        </a>

        <a href="{{ route('chauffeur.voyages.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'chauffeur.voyages.index') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-road"></i></div>
            <span>Voyages</span>
        </a>

        <a href="{{ route('chauffeur.reservations.scan') }}" class="nav-link {{ str_starts_with($currentRoute, 'chauffeur.reservations') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-qrcode"></i></div>
            <span>Scanner QR</span>
        </a>

        <a href="{{ route('chauffeur.messages.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'chauffeur.messages') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-envelope"></i></div>
            <span>Messages</span>
            @if($totalUnread > 0)
                <span class="nav-badge">{{ $totalUnread }}</span>
            @endif
        </a>

        <p class="nav-section-title" style="margin-top: 12px;">Historique</p>

        <a href="{{ route('chauffeur.voyages.history') }}" class="nav-link {{ $currentRoute === 'chauffeur.voyages.history' ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-history"></i></div>
            <span>Historique</span>
        </a>

        <a href="{{ route('chauffeur.signalements.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'chauffeur.signalements') ? 'active' : '' }}">
            <div class="nav-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <span>Signalements</span>
        </a>
    </nav>

    {{-- Logout --}}
    <div class="sidebar-footer">
        <a href="{{ route('chauffeur.logout') }}" class="nav-link logout-link">
            <div class="nav-icon"><i class="fas fa-sign-out-alt"></i></div>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>

{{-- Overlay mobile --}}
<div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleSidebar()"></div>

<style>
.chauffeur-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 260px;
    height: 100vh;
    background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    z-index: 1100;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow-y: auto;
    overflow-x: hidden;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.1) transparent;
}

.sidebar-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 20px 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none !important;
}

.brand-icon {
    width: 38px;
    height: 38px;
    background: linear-gradient(135deg, #e94e1a, #f97316);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    box-shadow: 0 4px 12px rgba(233, 78, 26, 0.3);
}

.brand-text {
    font-size: 1.3rem;
    font-weight: 800;
    color: white;
    letter-spacing: 1px;
}

.sidebar-close-btn {
    background: rgba(255,255,255,0.08);
    border: none;
    color: rgba(255,255,255,0.6);
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}
.sidebar-close-btn:hover {
    background: rgba(255,255,255,0.15);
    color: white;
}

.sidebar-profile {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 18px 20px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}

.profile-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    background: linear-gradient(135deg, #e94e1a, #d33d0f);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 15px;
    flex-shrink: 0;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(233,78,26,0.25);
}
.profile-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-info {
    min-width: 0;
}
.profile-name {
    font-weight: 700;
    color: white;
    font-size: 0.88rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.profile-role {
    font-size: 0.72rem;
    color: rgba(255,255,255,0.5);
    margin: 2px 0 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.sidebar-nav {
    padding: 12px 12px 0;
    flex: 1;
}

.nav-section-title {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: rgba(255,255,255,0.3);
    font-weight: 700;
    padding: 0 10px;
    margin: 0 0 6px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    color: rgba(255,255,255,0.6) !important;
    text-decoration: none !important;
    font-size: 0.88rem;
    font-weight: 500;
    margin-bottom: 3px;
    transition: all 0.2s ease;
    position: relative;
}

.nav-link:hover {
    background: rgba(255,255,255,0.06);
    color: rgba(255,255,255,0.9) !important;
}

.nav-link.active {
    background: rgba(233, 78, 26, 0.15);
    color: #f97316 !important;
    font-weight: 600;
}
.nav-link.active .nav-icon {
    color: #f97316;
}

.nav-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 14px;
    color: inherit;
    flex-shrink: 0;
}

.nav-badge {
    margin-left: auto;
    background: #e94e1a;
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 20px;
    min-width: 18px;
    text-align: center;
}

.sidebar-footer {
    padding: 12px;
    border-top: 1px solid rgba(255,255,255,0.06);
    margin-top: auto;
}

.logout-link {
    color: rgba(255,255,255,0.4) !important;
}
.logout-link:hover {
    background: rgba(239, 68, 68, 0.12) !important;
    color: #f87171 !important;
}

.sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1099;
    backdrop-filter: blur(2px);
}

/* Desktop: sidebar always visible */
@media (min-width: 768px) {
    .chauffeur-sidebar {
        transform: translateX(0) !important;
    }
}

/* Mobile: sidebar slides in */
@media (max-width: 767.98px) {
    .chauffeur-sidebar {
        transform: translateX(-100%);
        width: 280px;
    }
    .chauffeur-sidebar.open {
        transform: translateX(0);
    }
    .sidebar-overlay.open {
        display: block;
    }
}
</style>
