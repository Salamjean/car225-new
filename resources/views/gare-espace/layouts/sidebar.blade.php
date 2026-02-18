@php
    $gare = auth('gare')->user();
@endphp

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-72 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white shadow-2xl sidebar-transition -translate-x-full lg:translate-x-0">
    <!-- Header -->
    <div class="p-6 border-b border-gray-700/50">
        <div class="flex items-center gap-4">
            <div class="relative">
                @if($gare->profile_image)
                    <img src="{{ asset('storage/' . $gare->profile_image) }}" 
                         class="w-14 h-14 rounded-xl object-cover border-2 border-orange-500 shadow-lg" alt="Photo">
                @else
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        {{ strtoupper(substr($gare->responsable_prenom ?? 'G', 0, 1)) }}
                    </div>
                @endif
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-gray-900"></div>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-sm truncate">{{ $gare->responsable_prenom }} {{ $gare->responsable_nom }}</h3>
                <p class="text-xs text-gray-400 truncate">{{ $gare->nom_gare }}</p>
                <p class="text-xs text-orange-400 font-medium">Responsable de Gare</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-4 space-y-1 overflow-y-auto" style="max-height: calc(100vh - 200px);">
        <!-- Dashboard -->
        <a href="{{ route('gare-espace.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.dashboard') ? 'bg-orange-500/20 text-orange-400 shadow-lg' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-th-large w-5 text-center"></i>
            <span class="font-medium text-sm">Tableau de bord</span>
        </a>

        <!-- Séparateur -->
        <div class="py-2">
            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Gestion</p>
        </div>

        <!-- Voyages -->
        <a href="{{ route('gare-espace.voyages.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.voyages.*') ? 'bg-orange-500/20 text-orange-400 shadow-lg' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-route w-5 text-center"></i>
            <span class="font-medium text-sm">Programmer Voyages</span>
        </a>

        <!-- Personnel -->
        <a href="{{ route('gare-espace.personnel.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.personnel.*') ? 'bg-orange-500/20 text-orange-400 shadow-lg' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span class="font-medium text-sm">Personnel</span>
        </a>

        <!-- Véhicules -->
        <a href="{{ route('gare-espace.vehicules.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.vehicules.*') ? 'bg-orange-500/20 text-orange-400 shadow-lg' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-bus w-5 text-center"></i>
            <span class="font-medium text-sm">Véhicules</span>
        </a>

        <!-- Itinéraires -->
        <a href="{{ route('gare-espace.itineraire.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.itineraire.*') ? 'bg-orange-500/20 text-orange-400 shadow-lg' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-map-marked-alt w-5 text-center"></i>
            <span class="font-medium text-sm">Itinéraires</span>
        </a>

        <!-- Caisse -->
        <a href="{{ route('gare-espace.caisse.index') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.caisse.*') ? 'bg-orange-500/20 text-orange-400 shadow-lg' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-cash-register w-5 text-center"></i>
            <span class="font-medium text-sm">Caissières</span>
        </a>

        <!-- Séparateur -->
        <div class="py-2">
            <p class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Autres</p>
        </div>

        <!-- Historique Voyages -->
        <a href="{{ route('gare-espace.voyages.history') }}" 
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.voyages.history') ? 'bg-orange-500/20 text-orange-400 shadow-lg' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-history w-5 text-center"></i>
            <span class="font-medium text-sm">Historique Voyages</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700/50">
        <form action="{{ route('gare-espace.logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-red-400 hover:bg-red-500/10 rounded-xl transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="font-medium text-sm">Déconnexion</span>
            </button>
        </form>
    </div>
</aside>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>
