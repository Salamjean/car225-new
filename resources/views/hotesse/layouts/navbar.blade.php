<header class="mdc-top-app-bar bg-white border-b border-gray-200 shadow-sm" style="background-color: #fff; color: #333;">
  <div class="mdc-top-app-bar__row">
    <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-start">
      <button class="material-icons mdc-top-app-bar__navigation-icon mdc-icon-button sidebar-toggler text-gray-800 hover:text-[#e94e1a] transition-all" style="font-size: 24px;">notes</button>
    </div>
    <div class="mdc-top-app-bar__section mdc-top-app-bar__section--align-end mdc-top-app-bar__section-right">
      <!-- Version desktop (cachée sur mobile) -->
      <div class="menu-button-container menu-profile d-none d-md-block">
        <button class="mdc-button mdc-menu-button flex items-center gap-2 hover:bg-gray-50 rounded-lg px-2 py-1 transition-all">
          <span class="d-flex align-items-center">
            <span class="figure shadow-sm mr-2"
              style="width:36px; height:36px; border-radius:50%; background:#fcf0e8; display:flex; align-items:center; justify-content:center; color:#e94e1a; font-weight:bold; overflow: hidden; border: 1px solid #fee2d5;">
              @php
                $hotesse = Auth::guard('hotesse')->user();
              @endphp
              @if($hotesse->profile_picture)
                <img src="{{ asset('storage/' . $hotesse->profile_picture) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <i class="fas fa-user-circle text-xl"></i>
              @endif
            </span>
            <span class="text-sm font-bold text-gray-900 tracking-tight ml-1">Hôtesse</span>
            <i class="mdi mdi-chevron-down ml-1 text-gray-400"></i>
          </span>
        </button>
        <div class="mdc-menu mdc-menu-surface shadow-xl rounded-xl border border-gray-100 mt-2" tabindex="-1">
            <ul class="mdc-list py-2" role="menu" aria-hidden="true" aria-orientation="vertical">
                <li class="mdc-list-item hover:bg-orange-50 cursor-pointer" role="menuitem">
                  <a href="{{route('hotesse.profile')}}" class="d-flex align-items-center w-100 text-decoration-none text-gray-700 px-4 py-2">
                    <i class="mdi mdi-account-outline mr-3 text-[#e94e1a] text-lg"></i>
                    <span class="font-medium">Mon Profil</span>
                  </a>
                </li>
                <li class="mdc-list-divider my-1 border-gray-100"></li>
                <li class="mdc-list-item hover:bg-red-50 cursor-pointer" role="menuitem">
                  <a href="{{route('hotesse.logout')}}" class="d-flex align-items-center w-100 text-decoration-none text-red-600 px-4 py-2">
                    <i class="mdi mdi-logout mr-3 text-red-500 text-lg"></i>
                    <span class="font-medium">Déconnexion</span>
                  </a>
                </li>
            </ul>
        </div>
      </div>

      <!-- Version mobile -->
      <div class="d-md-none mobile-menu-container">
        <button class="mdc-icon-button text-gray-600 mobile-profile-toggle">
          <span class="figure shadow-sm"
            style="width:35px; height:35px; border-radius:50%; background:#fcf0e8; display:flex; align-items:center; justify-content:center; color:#e94e1a; font-weight:bold; font-size:14px; overflow: hidden; border: 1px solid #fee2d5;">
            @if($hotesse->profile_picture)
                <img src="{{ asset('storage/' . $hotesse->profile_picture) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <i class="fas fa-user-circle"></i>
            @endif
          </span>
        </button>
        <div class="mobile-menu mdc-menu mdc-menu-surface shadow-xl rounded-xl border border-gray-100 mt-2" tabindex="-1">
          <ul class="mdc-list py-2" role="menu" aria-hidden="true" aria-orientation="vertical">
            <li class="mobile-user-info mdc-list-item bg-gray-50 border-b border-gray-100 px-4 py-3" style="pointer-events: none;">
              <div class="d-flex align-items-center w-100">
                <span class="figure mr-3"
                  style="width:32px; height:32px; border-radius:50%; background:#fcf0e8; display:flex; align-items:center; justify-content:center; color:#e94e1a; font-weight:bold; font-size:12px; border: 1px solid #fee2d5;">
                  @if($hotesse->profile_picture)
                    <img src="{{ asset('storage/' . $hotesse->profile_picture) }}" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                  @else
                    <i class="fas fa-user"></i>
                  @endif
                </span>
                <div class="flex flex-col">
                    <span class="user-name font-bold text-gray-800 text-sm leading-tight">{{ $hotesse->name }} {{ $hotesse->prenom }}</span>
                    <span class="text-xs text-gray-500">Hôtesse</span>
                </div>
              </div>
            </li>
            <li class="mdc-list-item hover:bg-orange-50 cursor-pointer" role="menuitem">
              <a href="{{route('hotesse.profile')}}" class="d-flex align-items-center w-100 text-decoration-none text-gray-700 px-4 py-2">
                <i class="mdi mdi-account-outline mr-3 text-[#e94e1a] text-lg"></i>
                <span class="font-medium">Mon Profil</span>
              </a>
            </li>
            <li class="mdc-list-divider my-1 border-gray-100"></li>
            <li class="mdc-list-item hover:bg-red-50 cursor-pointer" role="menuitem">
              <a href="{{route('hotesse.logout')}}" class="d-flex align-items-center w-100 text-decoration-none text-red-600 px-4 py-2">
                <i class="mdi mdi-logout mr-3 text-red-500 text-lg"></i>
                <span class="font-medium">Déconnexion</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</header>

<style>
  .mdc-menu-button .ml-2 {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
  }

  .mdc-menu-button:focus .ml-2 {
    transform: rotate(180deg);
  }

  /* Styles pour la version mobile */
  .mobile-menu-container {
    position: relative;
  }

  .mobile-profile-toggle {
    padding: 8px;
  }

  .mobile-user-info {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    min-height: 60px;
  }

  .mobile-menu .mdc-list-item {
    min-height: 48px;
    display: flex;
    align-items: center;
  }

  /* Ajustement pour les écrans très petits */
  @media (max-width: 576px) {
    .mobile-menu .mdc-list-item {
      padding: 12px 16px;
    }

    .mobile-menu {
      min-width: 200px;
      right: 0 !important;
      left: auto !important;
    }

    .mobile-user-info .user-name {
      font-size: 0.9rem;
      font-weight: 500;
    }
  }

  /* Correction pour l'alignement des menus */
  .mdc-menu-surface {
    z-index: 1000;
  }

  .mobile-menu .mdc-list {
    padding: 0;
  }

  /* Assurer que les menus s'affichent à droite */
  .mdc-menu {
    position: absolute;
    right: 0;
    top: 100%;
    left: auto !important;
  }

  .menu-button-container {
    position: relative;
  }

  /* Forcer l'alignement à droite pour tous les menus */
  .mdc-menu-surface--open {
    right: 0 !important;
    left: auto !important;
  }
</style>

<script>
  // Initialisation du menu mobile
  document.addEventListener('DOMContentLoaded', function () {
    // Menu desktop
    const menuButton = document.querySelector('.mdc-menu-button');
    const menu = document.querySelector('.mdc-menu');

    // Menu mobile
    const mobileMenuButton = document.querySelector('.mobile-profile-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');

    // Initialiser le menu desktop
    if (menuButton && menu) {
      const menuInstance = new mdc.menu.MDCMenu(menu);
      // Forcer l'ancrage en bas à droite
      menuInstance.setAnchorCorner(mdc.menu.Corner.BOTTOM_END);
      menuInstance.setAnchorMargin({ top: 0, bottom: 0, left: 0, right: 0 });

      menuButton.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        menuInstance.open = !menuInstance.open;
      });
    }

    // Initialiser le menu mobile
    if (mobileMenuButton && mobileMenu) {
      const mobileMenuInstance = new mdc.menu.MDCMenu(mobileMenu);
      // Forcer l'ancrage en bas à droite
      mobileMenuInstance.setAnchorCorner(mdc.menu.Corner.BOTTOM_END);
      mobileMenuInstance.setAnchorMargin({ top: 0, bottom: 0, left: 0, right: 0 });

      mobileMenuButton.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        mobileMenuInstance.open = !mobileMenuInstance.open;
      });

      // Fermer le menu quand on clique sur un lien
      mobileMenu.addEventListener('click', (e) => {
        if (e.target.closest('a')) {
          setTimeout(() => {
            mobileMenuInstance.open = false;
          }, 100);
        }
      });
    }

    // Fermer les menus quand on clique ailleurs
    document.addEventListener('click', function (e) {
      if (menu && menuButton && !menuButton.contains(e.target) && !menu.contains(e.target)) {
        const menuInstance = mdc.menu.MDCMenu.attachTo(menu);
        menuInstance.open = false;
      }
      if (mobileMenu && mobileMenuButton && !mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
        const mobileMenuInstance = mdc.menu.MDCMenu.attachTo(mobileMenu);
        mobileMenuInstance.open = false;
      }
    });
  });

  // Correction supplémentaire pour forcer l'alignement à droite
  function fixMenuPosition() {
    const menus = document.querySelectorAll('.mdc-menu-surface');
    menus.forEach(menu => {
      menu.style.left = 'auto';
      menu.style.right = '0';
    });
  }

  // Appliquer la correction après le chargement et après chaque ouverture de menu
  document.addEventListener('DOMContentLoaded', fixMenuPosition);
  setTimeout(fixMenuPosition, 100);
</script>
