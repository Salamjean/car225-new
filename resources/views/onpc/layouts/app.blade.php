<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Espace ONPC') - CAR 225</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" />
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .nav-link.active { background:#1e293b; color:#fff; border-left:4px solid #1e3a8a; }
    </style>
</head>

<body class="bg-gray-50 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl z-20">
        <div class="h-20 flex items-center px-6 border-b border-slate-800 bg-blue-800">
            <div class="flex items-center gap-3">
                <i class="fas fa-shield-alt text-2xl"></i>
                <div>
                    <h1 class="font-bold text-lg leading-tight uppercase">ONPC</h1>
                    <p class="text-xs text-blue-200">Protection Civile</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 py-6 px-3 space-y-1 overflow-y-auto">
            @php $route = request()->route()?->getName(); @endphp

            <a href="{{ route('onpc.dashboard') }}"
                class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white transition-colors {{ $route === 'onpc.dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt w-6"></i>
                <span class="font-medium">Tableau de bord</span>
            </a>

            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-6 mb-2">Supervision</p>

            <a href="{{ route('onpc.sapeurs.index') }}"
                class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white transition-colors {{ str_starts_with($route ?? '', 'onpc.sapeurs') ? 'active' : '' }}">
                <i class="fas fa-fire-extinguisher w-6"></i>
                <span class="font-medium">Sapeurs Pompiers</span>
            </a>

            <a href="{{ route('onpc.signalements.index') }}"
                class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white transition-colors {{ str_starts_with($route ?? '', 'onpc.signalements') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle w-6"></i>
                <span class="font-medium">Signalements</span>
            </a>

            <a href="{{ route('onpc.evacues.index') }}"
                class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg text-gray-300 hover:bg-slate-800 hover:text-white transition-colors {{ str_starts_with($route ?? '', 'onpc.evacues') ? 'active' : '' }}">
                <i class="fas fa-procedures w-6"></i>
                <span class="font-medium">Passagers évacués</span>
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800">
            @php $u = Auth::guard('onpc')->user(); @endphp
            <div class="flex items-center gap-3 mb-4">
                @if($u && $u->photo_path)
                    <img src="{{ Storage::url($u->photo_path) }}" class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center font-bold">
                        {{ $u ? strtoupper(substr($u->name, 0, 1)) : '?' }}
                    </div>
                @endif
                <div class="overflow-hidden">
                    <p class="font-bold text-sm truncate">{{ $u->name ?? '—' }}</p>
                    <p class="text-xs text-green-400 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-400"></span> En ligne
                    </p>
                </div>
            </div>
            <div class="space-y-2">
                <a href="{{ route('onpc.profile') }}"
                    class="flex items-center justify-center gap-2 w-full py-2 bg-slate-800 hover:bg-slate-700 text-gray-200 text-sm font-medium rounded-lg border border-slate-700">
                    <i class="fas fa-user-cog"></i> Mon profil
                </a>
                <form method="POST" action="{{ route('onpc.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 bg-red-900/50 hover:bg-red-800 text-red-200 text-sm font-medium rounded-lg border border-red-900">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden">
        <main class="flex-1 overflow-y-auto bg-gray-50 p-6 lg:p-8">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4 rounded">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4 rounded">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

</body>

</html>
