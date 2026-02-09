<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Espace Hôtesse - CAR225</title>
  
  <!-- CSS -->
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/css/vendor.bundle.base.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/flag-icon-css/css/flag-icon.min.css')}}">
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/vendors/jvectormap/jquery-jvectormap.css')}}">
  <link rel="stylesheet" href="{{asset('assetsPoster/assets/css/demo/style.css')}}">
  <link rel="shortcut icon" href="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" />
</head>

<body>
  <script src="{{asset('assetsPoster/assets/js/preloader.js')}}"></script>
  <div class="body-wrapper">
    <!-- Sidebar -->
    @include('hotesse.layouts.sidebar')
    
    <div class="main-wrapper mdc-drawer-app-content">
      <!-- Navbar -->
      @include('hotesse.layouts.navbar')
      
      <!-- Main Content -->
      <div class="page-wrapper mdc-toolbar-fixed-adjust">
        @yield('content')
      </div>
    </div>
  </div>

  <!-- plugins:js -->
  <script src="{{asset('assetsPoster/assets/vendors/js/vendor.bundle.base.js')}}"></script>
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
</body>

</html>
