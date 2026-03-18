<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Espace Agent - CAR225</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" />
  
  <style>
    * { font-family: 'Inter', sans-serif; }
    
    .agent-main-wrapper {
      margin-left: 260px;
      padding-top: 64px;
      min-height: 100vh;
      background: #f8fafc;
      transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Sidebar réduite → le contenu se décale */
    body.sidebar-collapsed .agent-main-wrapper {
      margin-left: 68px;
    }
    
    @media (max-width: 767.98px) {
      .agent-main-wrapper {
        margin-left: 0 !important;
      }
    }
  </style>
  
  @yield('styles')
</head>

{{-- Script inline : restaurer l'état collapsed AVANT le rendu pour éviter le flash --}}
<script>
  (function() {
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
      // S'applique dès que possible
      document.addEventListener('DOMContentLoaded', function() {
        document.body.classList.add('sidebar-collapsed');
        var icon = document.getElementById('sidebarCollapseIcon');
        if (icon) {
          icon.classList.remove('fa-bars');
          icon.classList.add('fa-arrow-right');
        }
      });
    }
  })();
</script>

<body style="background: #f8fafc;">
  <!-- Sidebar -->
  @include('agent.layouts.sidebar')
  
  <!-- Navbar -->
  @include('agent.layouts.navbar')

  <!-- Main Content -->
  <div class="agent-main-wrapper">
    @yield('content')
  </div>

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  @yield('scripts')
  @stack('scripts')
</body>

</html>