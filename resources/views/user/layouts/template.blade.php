<!DOCTYPE html>
<html lang="fr" class="bg-[#F8F9FA]">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Espace Voyageur</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assetsPoster/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}" />
    @vite(['resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }
    </style>
    
    @stack('styles')
</head>

<body class="bg-[#F8F9FA] min-h-screen">
    
    <!-- Sidebar -->
    @include('user.layouts.sidebar')

    <!-- Main Content Area -->
    <div class="md:pl-64 flex flex-col min-h-screen">
        <!-- Navbar -->
        @include('user.layouts.navbar')

        <!-- Page Content -->
        <main class="flex-1 pt-24 pb-12">
            <div class="container mx-auto px-4 sm:px-8">
                @yield('content')
            </div>
        </main>

        <!-- Footer (Simple) -->
        <footer class="py-6 px-8 text-center sm:text-left border-t border-gray-100">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                &copy; {{ date('Y') }} CAR225. TOUS DROITS RÉSERVÉS.
            </p>
        </footer>
    </div>

    <!-- Scripts Core -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Chart.js and other plugins used in subpages -->
    @stack('scripts')
</body>

</html>