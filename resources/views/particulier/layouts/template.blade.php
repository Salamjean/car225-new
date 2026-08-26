<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Espace Transporteur') — CAR225</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Icons & Tailwind CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}">

    @yield('styles')

    <style>
        :root {
            --orange: #E94F1B;
            --orange-dark: #D44518;
            --sidebar-w: 260px;
            --nav-h: 64px;
            --bg: #F8F9FA;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg);
        }
    </style>
</head>
<body class="text-gray-800 antialiased min-h-screen flex">

    <!-- Sidebar -->
    <aside class="w-[var(--sidebar-w)] bg-[#001A41] text-white flex flex-col h-screen fixed top-0 left-0 z-50 transition-all duration-300 shadow-xl">
        <!-- Logo -->
        <div class="h-[var(--nav-h)] flex items-center px-6 border-b border-white/10">
            <a href="{{ route('particulier.dashboard') }}" class="flex items-center gap-2">
                <img src="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}" alt="Car225" class="bg-white rounded-full p-1 w-9 h-9">
                <span class="font-extrabold text-lg uppercase tracking-tight text-white">CAR225</span>
            </a>
        </div>

        <!-- User Info -->
        <div class="p-6 border-b border-white/5 text-center">
            <div class="w-16 h-16 rounded-full overflow-hidden mx-auto mb-3 border-2 border-white/20 bg-white/10">
                @if(Auth::guard('particulier')->user()->photo_proprietaire)
                    <img src="{{ Auth::guard('particulier')->user()->photo_proprietaire_url }}" alt="avatar" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center font-bold text-white bg-white/20 text-xl">
                        {{ substr(Auth::guard('particulier')->user()->name, 0, 1) }}
                    </div>
                @endif
            </div>
            <h4 class="font-bold text-sm text-white truncate">{{ Auth::guard('particulier')->user()->full_name }}</h4>
            <p class="text-xs text-white/55 font-semibold mt-0.5">{{ Auth::guard('particulier')->user()->code_id }}</p>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
            <a href="{{ route('particulier.dashboard') }}" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('particulier.dashboard') ? 'bg-[#e94f1b] text-white shadow-lg shadow-[#e94f1b]/10' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-chart-line w-5 text-center"></i>
                Tableau de bord
            </a>

            <a href="{{ route('particulier.profile') }}" 
                class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all {{ request()->routeIs('particulier.profile') ? 'bg-[#e94f1b] text-white shadow-lg shadow-[#e94f1b]/10' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                <i class="fas fa-id-card w-5 text-center"></i>
                Mon Profil & Véhicule
            </a>
        </nav>

        <!-- Footer / Logout -->
        <div class="p-4 border-t border-white/5">
            <form action="{{ route('particulier.logout') }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment vous déconnecter ?');">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-red-400 hover:bg-red-500/10 hover:text-red-300 rounded-xl transition-all">
                    <i class="fas fa-sign-out-alt w-5 text-center"></i>
                    Se déconnecter
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 ml-[var(--sidebar-w)] flex flex-col min-h-screen">
        <!-- Top Navbar -->
        <header class="h-[var(--nav-h)] bg-white border-b border-gray-200/80 flex items-center justify-between px-8 sticky top-0 z-40">
            <div>
                <h2 class="text-lg font-black text-gray-800 tracking-tight">@yield('page-title')</h2>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-xs font-semibold text-gray-400">{{ now()->format('d/m/Y H:i') }}</span>
                <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex items-center justify-center">
                    @if(Auth::guard('particulier')->user()->photo_proprietaire)
                        <img src="{{ Auth::guard('particulier')->user()->photo_proprietaire_url }}" alt="avatar" class="w-full h-full object-cover">
                    @else
                        <span class="font-bold text-xs text-gray-500">{{ substr(Auth::guard('particulier')->user()->name, 0, 1) }}</span>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
