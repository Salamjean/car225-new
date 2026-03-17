
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Espace - Compagnie</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/css/vendor.bundle.base.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>


  <!-- endinject -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/jvectormap/jquery-jvectormap.cs')}}s">
  <!-- End plugin css for this page -->
  <!-- Layout styles -->
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/css/demo/style.css')}}">
  <!-- End layout styles -->
   <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" />
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   @yield('styles')

   <style>
       /* =========================================
          Sidebar Layout & Responsiveness Fixes
          ========================================= */
       /* Enable scrolling on the sidebar content */
       .mdc-drawer__content {
           flex: 1;
           height: auto;
           overflow-y: auto !important;
           overflow-x: hidden;
       }

        /* Custom Scrollbar for sidebar */
        .mdc-drawer__content {
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0) transparent;
            transition: scrollbar-color 0.3s ease;
        }
        .mdc-drawer__content:hover {
            scrollbar-color: #cbd5e1 transparent;
        }
        .mdc-drawer__content::-webkit-scrollbar {
            width: 5px;
        }
        .mdc-drawer__content::-webkit-scrollbar-track {
            background: transparent; 
        }
        .mdc-drawer__content::-webkit-scrollbar-thumb {
            background: transparent; 
            border-radius: 10px;
        }
        .mdc-drawer__content:hover::-webkit-scrollbar-thumb {
            background: #cbd5e1; 
        }

       /* Fixed positioning for the drawer */
       .mdc-drawer {
           position: fixed !important;
           top: 0;
           left: 0;
           height: 100vh !important;
           z-index: 1000;
           display: flex;
           flex-direction: column;
           overflow: hidden !important;
       }

        /* Ensure main content is pushed by the sidebar width on desktop */
        .mdc-drawer-app-content {
            margin-left: 250px !important;
            width: calc(100% - 250px) !important;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.2s cubic-bezier(0.4, 0, 0.2, 1), width 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Navbar positioning */
        .mdc-top-app-bar {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            left: 250px !important;
            width: calc(100% - 250px) !important;
            z-index: 998 !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Fix gap above content adjustment */
        .mdc-toolbar-fixed-adjust {
            padding-top: 64px !important; /* Standard height of MDC top app bar */
        }

        /* Sidebar Backdrop Overlay default state (hidden) */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1001; /* Above drawer and navbar */
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile / Tablet Overlay Behavior */
        @media (max-width: 991px) {
            /* Sidebar is hidden by default and acts as an overlay */
            .mdc-drawer {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 4px 0 24px rgba(0,0,0,0.1) !important;
            }
            /* When open, it slides in */
            .mdc-drawer.mdc-drawer--open {
                transform: translateX(0);
            }
            
            /* Main content takes full width */
            .mdc-drawer-app-content {
                margin-left: 0 !important;
                width: 100% !important;
            }
            
            /* Navbar takes full width */
            .mdc-top-app-bar {
                width: 100% !important;
                left: 0 !important;
            }
        }

       /* =========================================
          Premium Styles (Moved from sidebar)
          ========================================= */
       @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
       
       /* Styles premium pour la Sidebar */
       .mdc-drawer {
           background-color: #ffffff !important;
           border-right: 1px solid #f1f5f9 !important;
           box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02) !important;
           font-family: 'Inter', sans-serif;
           width: 250px !important; /* Set fixed width to match margin adjustments */
       }
       .user-info {
           background: linear-gradient(to bottom, #f8fafc, #ffffff);
           padding: 24px 0 16px 0 !important;
           border-bottom: 1px solid #f1f5f9;
           margin-bottom: 16px;
           box-shadow: 0 4px 10px rgba(0,0,0,0.01);
       }
       .user-info .name {
           font-weight: 800 !important;
           color: #0f172a !important;
           font-size: 0.95rem !important;
           letter-spacing: -0.2px;
           margin-bottom: 4px;
       }
       .user-info .email {
           color: #64748b !important;
           font-size: 0.70rem !important;
           font-weight: 600 !important;
           letter-spacing: 0.5px;
       }
       .brand-logo img, .default-company-logo {
           box-shadow: 0 4px 14px rgba(234, 88, 12, 0.15) !important;
           border: 2px solid #ffffff !important;
           transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
       }
       .brand-logo:hover img, .brand-logo:hover .default-company-logo {
           transform: scale(1.05) translateY(-2px);
       }
       
       /* Elements de la liste */
       .mdc-drawer .mdc-list-item {
           margin: 4px 16px !important;
           border-radius: 12px !important;
           height: auto !important; /* Autoriser l'extension pour les sous-menus */
           min-height: 46px !important;
           display: block !important; /* Pour que le contenu s'empile verticalement */
           padding: 0 !important;
           transition: all 0.2s ease;
           overflow: hidden !important;
       }
       
       /* Désactiver l'effet d'ondulation (ripple) gris natif de Material Design */
       .mdc-drawer .mdc-list-item::before,
       .mdc-drawer .mdc-list-item::after {
           display: none !important;
           content: none !important;
       }

       .mdc-drawer .mdc-list-item:active,
       .mdc-drawer .mdc-list-item:focus {
            background-color: transparent !important;
       }
       
       /* Only apply hover effect specifically where needed */
       .mdc-drawer > .mdc-drawer__content > .mdc-list-group > .mdc-list-item:hover,
       .mdc-drawer-submenu .mdc-list-item:hover {
           background-color: #fff7ed !important;
       }

       .mdc-drawer-link, .mdc-expansion-panel-link {
           height: 46px !important;
           display: flex !important;
           align-items: center;
           padding: 0 16px !important;
           text-decoration: none !important;
       }
       .mdc-drawer .mdc-list-item:hover > .mdc-drawer-link,
       .mdc-drawer .mdc-list-item:hover > .mdc-expansion-panel-link {
           color: #ea580c !important;
       }
       .mdc-drawer .mdc-list-item:hover > .mdc-drawer-link .mdc-drawer-item-icon,
       .mdc-drawer .mdc-list-item:hover > .mdc-expansion-panel-link .mdc-drawer-item-icon {
           color: #ea580c !important;
           transform: scale(1.1);
       }
       
       /* Liens et Texte */
       .mdc-drawer-link, .mdc-expansion-panel-link {
           color: #475569 !important; /* Slate 600 */
           font-weight: 600 !important;
           font-size: 0.825rem !important;
           letter-spacing: 0.3px;
           width: 100%;
           transition: color 0.2s ease;
       }
       .mdc-drawer-item-icon {
           color: #94a3b8 !important; /* Slate 400 */
           font-size: 1.15rem !important;
           width: 24px;
           text-align: center;
           margin-right: 14px !important;
           transition: all 0.2s ease;
       }

       /* Badges Notifications */
       .sidebar-signalement-badge,
       .badge-danger {
           background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
           box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25) !important;
           border: none !important;
           padding: 3px 8px !important;
           border-radius: 20px !important;
           font-size: 0.65rem !important;
           font-weight: 800 !important;
       }

       /* Flèche des sous-menus */
       .mdc-drawer-arrow {
           color: #cbd5e1 !important;
           transition: transform 0.3s ease, color 0.2s ease;
           margin-left: auto !important;
       }
       .mdc-list-item:hover > .mdc-expansion-panel-link .mdc-drawer-arrow {
           color: #ea580c !important;
       }

       /* Sous-menus */
       .mdc-expansion-panel {
           background-color: transparent !important;
           padding-top: 0px;
           padding-bottom: 8px;
           display: none; 
       }
       
       .mdc-drawer-submenu .mdc-list-item {
           margin: 2px 12px 2px 32px !important;
           height: 38px !important;
           min-height: 38px !important;
           border-radius: 8px !important;
           display: flex !important; /* Les sous-items restent en flex */
           align-items: center !important;
       }
       .mdc-drawer-submenu .mdc-drawer-link {
           font-weight: 500 !important;
           color: #64748b !important;
           font-size: 0.8rem !important;
           position: relative;
           transition: all 0.2s ease;
       }
       .mdc-drawer-submenu .mdc-drawer-link::before {
           content: '';
           position: absolute;
           left: -16px;
           top: 50%;
           transform: translateY(-50%);
           width: 4px;
           height: 4px;
           background-color: #cbd5e1;
           border-radius: 50%;
           transition: all 0.2s ease;
       }
       .mdc-drawer-submenu .mdc-list-item:hover .mdc-drawer-link {
           color: #ea580c !important;
           padding-left: 4px;
       }
       .mdc-drawer-submenu .mdc-list-item:hover .mdc-drawer-link::before {
           background-color: #ea580c;
           transform: translateY(-50%) scale(1.5);
       }
       
       /* Pulse Animation */
       @keyframes pulse {
           0% {
               transform: scale(0.95);
               box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
           }
           70% {
               transform: scale(1);
               box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
           }
           100% {
               transform: scale(0.95);
               box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
           }
       }
   </style>
</head>
<body>
<script src="{{asset('assetsPoster/assets/js/preloader.js')}}"></script>
  <div class="body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    @include('compagnie.layouts.sidebar')
    <!-- partial -->
    <div class="main-wrapper mdc-drawer-app-content">
      <!-- partial:partials/_navbar.html -->
      @include('compagnie.layouts.navbar')
      <!-- partial -->
      <div class="page-wrapper mdc-toolbar-fixed-adjust">
        @yield('content')
      </div>
    </div>
  </div>
  <!-- plugins:js -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <script src="{{asset('assetsPoster/assets/vendors/js/vendor.bundle.base.js')}} "></script>
  <!-- endinject -->
  <!-- Plugin js for this page-->
  <script src="{{asset('assetsPoster/assets/vendors/chartjs/Chart.min.js')}}"></script>
  <script src="{{asset('assetsPoster/assets/vendors/jvectormap/jquery-jvectormap.min.js')}}"></script>
  <script src="{{asset('assetsPoster/assets/vendors/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
  <!-- End plugin js for this page-->
  <!-- inject:js -->
  <script src="{{asset('assetsPoster/assets/js/material.js')}}"></script>
  <script src="{{asset('assetsPoster/assets/js/misc.js')}}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{asset('assetsPoster/assets/js/dashboard.js')}}"></script>
  <!-- End custom js for this page-->
  
  <script>
    // Handle Mobile Sidebar Backdrop
    document.addEventListener('DOMContentLoaded', function() {
      const drawerEl = document.querySelector('.mdc-drawer');
      const backdrop = document.getElementById('sidebarBackdrop');
      
      if (!drawerEl || !backdrop) return;

      // Function to check screen size and drawer state
      const updateBackdrop = () => {
        if (window.innerWidth <= 991) {
          if (drawerEl.classList.contains('mdc-drawer--open')) {
            backdrop.classList.add('active');
          } else {
            backdrop.classList.remove('active');
          }
        } else {
          backdrop.classList.remove('active');
        }
      };

      // Watch for class changes on the drawer
      const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
          if (mutation.attributeName === 'class') {
            updateBackdrop();
          }
        });
      });
      
      observer.observe(drawerEl, { attributes: true });

      // Click on backdrop to close sidebar
      backdrop.addEventListener('click', () => {
        if (drawerEl.classList.contains('mdc-drawer--open')) {
          drawerEl.classList.remove('mdc-drawer--open');
          backdrop.classList.remove('active');
        }
      });

      // Initial check
      updateBackdrop();
      window.addEventListener('resize', updateBackdrop);
    });
  </script>

  @yield('scripts')
</body>
</html>