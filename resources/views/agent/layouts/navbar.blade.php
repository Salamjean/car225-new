@php
    $user = Auth::guard('agent')->user();
    $initials = strtoupper(substr($user->name ?? '', 0, 1)) . strtoupper(substr($user->prenom ?? '', 0, 1));
@endphp

<header class="agent-navbar">
    <div class="navbar-inner">
        {{-- Left: Menu toggle (mobile) + Page title --}}
        <div class="navbar-left">
            {{-- Toggle mobile --}}
            <button class="navbar-toggle d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            {{-- Toggle collapse sidebar (bureau) --}}
            <button class="navbar-toggle d-none d-md-flex" id="sidebarCollapseBtn" onclick="toggleSidebarCollapse()" title="Réduire la sidebar">
                <i class="fas fa-bars" id="sidebarCollapseIcon"></i>
            </button>
            <div class="navbar-title d-none d-md-block">
                @yield('title', 'Espace Agent')
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="navbar-right">
            {{-- Notifications --}}
            <a href="{{ route('agent.messages.index') }}" class="navbar-icon-btn" title="Messages">
                <i class="fas fa-bell"></i>
                @php
                    $unreadC = $user->messages()->where('is_read', false)->count();
                    $unreadG = $user->receivedGareMessages()->where('is_read', false)->count();
                    $totalU = $unreadC + $unreadG;
                @endphp
                @if($totalU > 0)
                    <span class="notification-dot"></span>
                @endif
            </a>

            {{-- Profile dropdown --}}
            <div class="profile-dropdown-wrapper">
                <button class="profile-trigger" onclick="toggleProfileDropdown()">
                    <div class="trigger-avatar">
                        @if($user->profile_picture)
                            <img src="{{ asset('storage/' . $user->profile_picture) }}" alt="Profile">
                        @else
                            {{ $initials }}
                        @endif
                    </div>
                    <span class="trigger-name d-none d-md-inline">{{ $user->prenom ?? $user->name }}</span>
                    <i class="fas fa-chevron-down trigger-arrow d-none d-md-inline"></i>
                </button>
                <div class="profile-dropdown" id="profileDropdown">
                    <div class="dropdown-header">
                        <p class="dropdown-user-name">{{ $user->prenom }} {{ $user->name }}</p>
                        <p class="dropdown-user-role">Agent</p>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('agent.profile') }}" class="dropdown-item">
                        <i class="fas fa-user-circle"></i>
                        Mon Profil
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('agent.logout') }}" class="dropdown-item logout-item">
                        <i class="fas fa-sign-out-alt"></i>
                        Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.agent-navbar {
    position: fixed;
    top: 0;
    left: 260px;
    right: 0;
    height: 64px;
    background: white;
    border-bottom: 1px solid #f0f0f0;
    z-index: 1050;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.navbar-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 100%;
    padding: 0 24px;
}

.navbar-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.navbar-toggle {
    background: none;
    border: none;
    font-size: 20px;
    color: #374151;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.navbar-toggle:hover { background: #f3f4f6; }

.navbar-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: #1f2937;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 8px;
}

.navbar-icon-btn {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280 !important;
    text-decoration: none !important;
    transition: all 0.2s;
    position: relative;
}
.navbar-icon-btn:hover {
    background: #f3f4f6;
    color: #e94e1a !important;
}

.notification-dot {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 8px;
    height: 8px;
    background: #ef4444;
    border-radius: 50%;
    border: 2px solid white;
}

.profile-dropdown-wrapper { position: relative; }

.profile-trigger {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 5px 12px 5px 5px;
    cursor: pointer;
    transition: all 0.2s;
}
.profile-trigger:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.trigger-avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: linear-gradient(135deg, #e94e1a, #d33d0f);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 12px;
    overflow: hidden;
    flex-shrink: 0;
}
.trigger-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.trigger-name {
    font-weight: 600;
    font-size: 0.85rem;
    color: #374151;
}

.trigger-arrow {
    font-size: 10px;
    color: #9ca3af;
    transition: transform 0.2s;
}

.profile-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.12);
    border: 1px solid #e5e7eb;
    min-width: 200px;
    overflow: hidden;
    z-index: 1200;
}
.profile-dropdown.open { display: block; }

.dropdown-header { padding: 14px 16px; }
.dropdown-user-name {
    font-weight: 700;
    color: #1f2937;
    font-size: 0.9rem;
    margin: 0;
}
.dropdown-user-role {
    color: #9ca3af;
    font-size: 0.75rem;
    margin: 2px 0 0;
}

.dropdown-divider {
    height: 1px;
    background: #f0f0f0;
}

.profile-dropdown .dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    color: #6b7280 !important;
    text-decoration: none !important;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s;
}
.profile-dropdown .dropdown-item:hover {
    background: #fef2f2;
    color: #ef4444 !important;
}

@media (max-width: 767.98px) {
    .agent-navbar { left: 0; }
}

/* ---- Sidebar collapsed (bureau) ---- */
body.sidebar-collapsed .agent-navbar  { left: 68px; }
body.sidebar-collapsed .main-content,
body.sidebar-collapsed .content-area,
body.sidebar-collapsed main           { margin-left: 68px; }
</style>

<script>
function toggleSidebar() {
    var sidebar = document.getElementById('agentSidebar');
    var overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('open');
    overlay.classList.toggle('open');
}

function toggleSidebarCollapse() {
    document.body.classList.toggle('sidebar-collapsed');
    var icon = document.getElementById('sidebarCollapseIcon');
    if (document.body.classList.contains('sidebar-collapsed')) {
        icon.classList.remove('fa-bars');
        icon.classList.add('fa-arrow-right');
    } else {
        icon.classList.remove('fa-arrow-right');
        icon.classList.add('fa-bars');
    }
    localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed'));
}

function toggleProfileDropdown() {
    var dd = document.getElementById('profileDropdown');
    dd.classList.toggle('open');
}

document.addEventListener('click', function(e) {
    var dd = document.getElementById('profileDropdown');
    var wrapper = e.target.closest('.profile-dropdown-wrapper');
    if (!wrapper && dd) dd.classList.remove('open');
});
</script>
