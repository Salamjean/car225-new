
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Espace - Admin</title>
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
<!-- Admin Layout Overrides -->
<style>
  /* ============================================
     SIDEBAR - Fixed full-height with scroll
     ============================================ */
  .mdc-drawer.mdc-drawer--dismissible {
    position: fixed !important;
    top: 0;
    bottom: 0;
    left: 0;
    height: 100vh !important;
    width: 250px;
    background: #ffffff !important;
    border-right: 1px solid #e5e7eb;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.04);
    z-index: 1000;
    overflow: visible !important;
    display: flex !important;
    flex-direction: column;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .mdc-drawer.mdc-drawer--dismissible:not(.mdc-drawer--open) {
    display: flex !important;
    transform: translateX(-100%);
  }

  .mdc-drawer.mdc-drawer--dismissible.mdc-drawer--open {
    transform: translateX(0);
  }

  /* Drawer header - fixed at top */
  .mdc-drawer .mdc-drawer__header {
    flex-shrink: 0;
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
  }

  .mdc-drawer .mdc-drawer__header .brand-logo img {
    width: 55% !important;
    margin-left: 30px !important;
  }

  /* Drawer content - scrollable */
  .mdc-drawer .mdc-drawer__content {
    flex: 1;
    overflow-y: auto !important;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 20px;
  }

  /* Custom scrollbar for sidebar */
  .mdc-drawer .mdc-drawer__content::-webkit-scrollbar {
    width: 4px;
  }
  .mdc-drawer .mdc-drawer__content::-webkit-scrollbar-track {
    background: transparent;
  }
  .mdc-drawer .mdc-drawer__content::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
  }
  .mdc-drawer .mdc-drawer__content::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
  }

  /* User info section */
  .mdc-drawer .user-info {
    padding: 12px 20px 16px;
    border-bottom: 1px solid #f3f4f6;
    margin: 0 !important;
    margin-bottom: 8px !important;
  }
  .mdc-drawer .user-info .name {
    font-size: 0.9rem;
    font-weight: 600;
    color: #1f2937 !important;
  }
  .mdc-drawer .user-info .email {
    font-size: 0.75rem;
    color: #6b7280 !important;
  }

  /* Sidebar menu items */
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item {
    margin: 2px 12px !important;
  }
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-drawer-link,
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-expansion-panel-link {
    height: 42px;
    line-height: 42px;
    padding: 0 12px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    color: #374151;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-drawer-link:hover,
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-expansion-panel-link:hover {
    background-color: #f3f4f6;
    color: #e94f1b;
  }
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-drawer-link.active {
    background: linear-gradient(135deg, #fff7ed, #ffedd5) !important;
    color: #e94f1b !important;
    font-weight: 600;
    border-left: 3px solid #e94f1b;
  }
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-drawer-link i,
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-expansion-panel-link i {
    font-size: 1.1rem;
    width: 24px;
    text-align: center;
    margin-right: 10px;
    color: #6b7280;
  }
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-drawer-link:hover i,
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-expansion-panel-link:hover i {
    color: #e94f1b;
  }
  .mdc-drawer .mdc-drawer-menu .mdc-drawer-item .mdc-drawer-link.active i {
    color: #e94f1b !important;
  }

  /* Submenu items */
  .mdc-drawer .mdc-drawer-submenu .mdc-drawer-item {
    margin: 0 0 0 20px !important;
  }
  .mdc-drawer .mdc-drawer-submenu .mdc-drawer-link {
    font-size: 0.8rem !important;
    height: 36px !important;
    line-height: 36px !important;
    color: #6b7280 !important;
  }
  .mdc-drawer .mdc-drawer-submenu .mdc-drawer-link:hover {
    color: #e94f1b !important;
  }

  /* ============================================
     MAIN CONTENT AREA - Offset by sidebar
     ============================================ */
  .body-wrapper {
    display: flex;
    min-height: 100vh;
  }

  .mdc-drawer.mdc-drawer--open:not(.mdc-drawer--closing) + .mdc-drawer-app-content {
    margin-left: 250px !important;
  }

  .main-wrapper.mdc-drawer-app-content {
    flex: 1;
    min-width: 0;
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* Navbar - offset by sidebar on desktop */
  .mdc-drawer.mdc-drawer--open:not(.mdc-drawer--closing) + .mdc-drawer-app-content .mdc-top-app-bar {
    margin-left: 250px;
    width: calc(100% - 250px) !important;
  }

  /* ============================================
     SIDEBAR BACKDROP (mobile overlay)
     ============================================ */
  .sidebar-backdrop {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .sidebar-backdrop.active {
    display: block;
    opacity: 1;
  }

  /* ============================================
     MOBILE RESPONSIVE (≤991px)
     ============================================ */
  @media (max-width: 991px) {
    .mdc-drawer.mdc-drawer--dismissible {
      transform: translateX(-100%);
      box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
    }

    .mdc-drawer.mdc-drawer--dismissible.mdc-drawer--open {
      transform: translateX(0);
    }

    /* Content takes full width on mobile */
    .mdc-drawer.mdc-drawer--open:not(.mdc-drawer--closing) + .mdc-drawer-app-content {
      margin-left: 0 !important;
    }

    .mdc-drawer.mdc-drawer--open:not(.mdc-drawer--closing) + .mdc-drawer-app-content .mdc-top-app-bar {
      margin-left: 0 !important;
      width: 100% !important;
    }

    .main-wrapper.mdc-drawer-app-content {
      margin-left: 0 !important;
    }
  }

  /* ============================================
     SMALL MOBILE (≤576px)
     ============================================ */
  @media (max-width: 576px) {
    .mdc-drawer.mdc-drawer--dismissible {
      width: 260px;
    }

    .body-wrapper .main-wrapper .page-wrapper .content-wrapper {
      padding: 12px !important;
    }
  }

  /* ============================================
     PULSE DOT ANIMATION
     ============================================ */
  @keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.5); }
  }
</style>
</head>
<body>
<script src="{{asset('assetsPoster/assets/js/preloader.js')}}"></script>
  <div class="body-wrapper">
    <!-- partial:partials/_sidebar.html -->
    @include('admin.layouts.sidebar')
    <!-- partial -->
    <div class="main-wrapper mdc-drawer-app-content">
      <!-- partial:partials/_navbar.html -->
      @include('admin.layouts.navbar')
      <!-- partial -->
      <div class="page-wrapper mdc-toolbar-fixed-adjust">
        @yield('content')
      </div>
    </div>
  </div>
  <!-- plugins:js -->
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

  <!-- Sidebar backdrop logic for mobile -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var backdrop = document.getElementById('sidebarBackdrop');
      var drawer = document.querySelector('.mdc-drawer');

      if (backdrop && drawer) {
        // Watch for drawer open/close
        var observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.attributeName === 'class') {
              var isOpen = drawer.classList.contains('mdc-drawer--open');
              var isMobile = window.matchMedia('(max-width: 991px)').matches;
              if (isOpen && isMobile) {
                backdrop.classList.add('active');
              } else {
                backdrop.classList.remove('active');
              }
            }
          });
        });
        observer.observe(drawer, { attributes: true });

        // Close drawer on backdrop click
        backdrop.addEventListener('click', function() {
          if (typeof mdc !== 'undefined' && mdc.drawer) {
            var drawerInstance = mdc.drawer.MDCDrawer.attachTo(drawer);
            drawerInstance.open = false;
          }
          backdrop.classList.remove('active');
        });
      }
    });
  </script>
</body>
</html> 