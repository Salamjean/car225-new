@php
    $gare = auth('gare')->user();

    $liveCount = \App\Models\Voyage::where('statut', 'en_cours')
        ->whereHas('programme', function ($q) use ($gare) {
            $q->where('compagnie_id', $gare->compagnie_id)
              ->where(function ($q2) use ($gare) {
                  $q2->where('gare_depart_id', $gare->id)
                     ->orWhere('gare_arrivee_id', $gare->id);
              });
        })->count();

    $reservationOpen = request()->routeIs('gare-espace.reservations.*');
@endphp

<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar" class="fixed top-0 left-0 z-50 h-full w-72 bg-gradient-to-b from-gray-900 via-gray-800 to-gray-900 text-white shadow-2xl sidebar-transition -translate-x-full lg:translate-x-0 flex flex-col">

    <!-- Header -->
    <div class="p-6 border-b border-gray-700/50 flex-shrink-0">
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
    <nav class="flex-1 overflow-y-auto p-4 space-y-1">

        <!-- Dashboard -->
        <a href="{{ route('gare-espace.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.dashboard') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-th-large w-5 text-center"></i>
            <span class="font-medium text-sm">Tableau de bord</span>
        </a>

        <!-- Suivi en temps réel -->
        <a href="{{ route('gare-espace.tracking.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.tracking.*') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-satellite-dish w-5 text-center"></i>
            <span class="font-medium text-sm flex-1">Suivi en temps réel</span>
            @if($liveCount > 0)
                <span class="bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full animate-pulse">LIVE</span>
            @endif
        </a>

        <!-- Voyages -->
        <a href="{{ route('gare-espace.voyages.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.voyages.index') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-route w-5 text-center"></i>
            <span class="font-medium text-sm">Assigner des Voyages</span>
        </a>

        <!-- Réservations (collapsible) -->
        <div>
            <button onclick="toggleReservations()"
                class="flex items-center gap-3 w-full px-4 py-3 rounded-xl transition-all duration-200 {{ $reservationOpen ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <i class="fas fa-ticket-alt w-5 text-center"></i>
                <span class="font-medium text-sm flex-1 text-left">Réservations</span>
                <i id="res-chevron" class="fas fa-chevron-down text-[10px] transition-transform duration-200 {{ $reservationOpen ? 'rotate-180' : '' }}"></i>
            </button>
            <div id="res-submenu" class="{{ $reservationOpen ? '' : 'hidden' }} pl-12 pt-1 space-y-1 overflow-hidden">
                <a href="{{ route('gare-espace.reservations.index', ['tab' => 'en-cours']) }}"
                   class="block py-2 text-xs font-medium transition-colors {{ request('tab') == 'en-cours' || (!request()->has('tab') && request()->routeIs('gare-espace.reservations.index')) ? 'text-orange-400' : 'text-gray-400 hover:text-white' }}">
                    Réservés
                </a>
                <a href="{{ route('gare-espace.reservations.index', ['tab' => 'terminees']) }}"
                   class="block py-2 text-xs font-medium transition-colors {{ request('tab') == 'terminees' ? 'text-orange-400' : 'text-gray-400 hover:text-white' }}">
                    Embarqués
                </a>
                <a href="{{ route('gare-espace.reservations.index', ['tab' => 'details']) }}"
                   class="block py-2 text-xs font-medium transition-colors {{ request('tab') == 'details' ? 'text-orange-400' : 'text-gray-400 hover:text-white' }}">
                    Détails & Stats
                </a>
            </div>
        </div>

        <!-- Personnel -->
        <a href="{{ route('gare-espace.personnel.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.personnel.*') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-users w-5 text-center"></i>
            <span class="font-medium text-sm">Personnel</span>
        </a>

        <!-- Véhicules -->
        <a href="{{ route('gare-espace.vehicules.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.vehicules.*') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-bus w-5 text-center"></i>
            <span class="font-medium text-sm">Véhicules</span>
        </a>

        <!-- Itinéraires -->
        <a href="{{ route('gare-espace.itineraire.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.itineraire.*') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-map-marked-alt w-5 text-center"></i>
            <span class="font-medium text-sm">Itinéraires</span>
        </a>

        <!-- Programmes -->
        <a href="{{ route('gare-espace.programme.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.programme.*') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-calendar-alt w-5 text-center"></i>
            <span class="font-medium text-sm">Programmes</span>
        </a>

        <!-- Agents -->
        <a href="{{ route('gare-espace.agents.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.agents.*') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-user-tie w-5 text-center"></i>
            <span class="font-medium text-sm">Agents</span>
        </a>

        <!-- Boîte de réception -->
        <a href="{{ route('gare-espace.messages.index') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.messages.*') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-envelope w-5 text-center"></i>
            <span class="font-medium text-sm flex-1">Boîte de réception</span>
            @php
                $unreadMessages = \App\Models\CompanyMessage::where('recipient_type', 'App\Models\Gare')
                    ->where('recipient_id', $gare->id)
                    ->where('is_read', false)->count();
                $unreadStaff = \App\Models\GareMessage::where('gare_id', $gare->id)
                    ->whereNotNull('sender_type')
                    ->where('is_read', false)->count();
                $totalUnread = $unreadMessages + $unreadStaff;
            @endphp
            @if($totalUnread > 0)
                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">{{ $totalUnread }}</span>
            @endif
        </a>

        <!-- Historique Voyages -->
        <a href="{{ route('gare-espace.voyages.history') }}"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('gare-espace.voyages.history') ? 'bg-orange-500/20 text-orange-400' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <i class="fas fa-history w-5 text-center"></i>
            <span class="font-medium text-sm">Historique Voyages</span>
        </a>

    </nav>

    <!-- Footer -->
    <div class="flex-shrink-0 p-4 border-t border-gray-700/50">
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

function toggleReservations() {
    const menu    = document.getElementById('res-submenu');
    const chevron = document.getElementById('res-chevron');
    menu.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
}
</script>
