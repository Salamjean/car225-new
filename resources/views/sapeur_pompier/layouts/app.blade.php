<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sapeurs Pompiers - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{asset('assetsPoster/assets/images/logo_car225.png')}}" rel="icon">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white flex flex-col shadow-2xl z-20">
        <!-- Brand -->
        <div class="h-20 flex items-center px-6 border-b border-gray-800 bg-red-700">
            <div class="flex items-center gap-3">
                <i class="fas fa-fire-extinguisher text-2xl"></i>
                <div>
                    <h1 class="font-bold text-lg leading-tight uppercase">Sapeurs</h1>
                    <p class="text-xs text-red-200">Pompiers</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 py-6 px-3 space-y-2 overflow-y-auto">
            <a href="{{ route('sapeur-pompier.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition-colors border-l-4 border-red-600">
                <i class="fas fa-tachometer-alt w-6"></i>
                <span class="font-medium">Tableau de bord</span>
            </a>

            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Signalements</p>

            <a href="{{ route('sapeur-pompier.dashboard', ['status' => 'nouveau']) }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                <i class="fas fa-exclamation-circle w-6"></i>
                <span class="font-medium">Nouveaux</span>
                <span class="ml-auto bg-red-600 text-white text-xs font-bold px-2 py-0.5 rounded-full">
                    {{ \App\Models\Signalement::where('sapeur_pompier_id', Auth::guard('sapeur_pompier')->id())->where('statut', 'nouveau')->count() }}
                </span>
            </a>

            <a href="{{ route('sapeur-pompier.dashboard', ['status' => 'traite']) }}"
                class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                <i class="fas fa-check-circle w-6"></i>
                <span class="font-medium">Traités</span>
            </a>
        </nav>

        <!-- User Footer -->
        <div class="p-4 border-t border-gray-800 bg-gray-900">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center font-bold text-lg">
                    {{ substr(Auth::guard('sapeur_pompier')->user()->name, 0, 1) }}
                </div>
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate">{{ Auth::guard('sapeur_pompier')->user()->name }}</p>
                    <p class="text-xs text-green-400 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span> En ligne
                    </p>
                </div>
            </div>
            <a href="{{ route('sapeur-pompier.logout') }}"
                class="flex items-center justify-center gap-2 w-full py-2 bg-red-900/50 hover:bg-red-800 text-red-200 text-sm font-medium rounded-lg transition-colors border border-red-900">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden">
        <!-- Mobile Header (Visible only on mobile) -->
        <header class="bg-white shadow-sm h-16 flex items-center px-6 lg:hidden justify-between z-10">
            <button class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <span class="font-bold text-gray-800">Sapeurs Pompiers</span>
        </header>

        <!-- Content Scrollable Area -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

</body>

</html>