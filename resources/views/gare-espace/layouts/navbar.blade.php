@php
    $gare = auth('gare')->user();
@endphp

<nav class="fixed top-0 right-0 left-0 lg:left-72 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-200 shadow-sm">
    <div class="flex items-center justify-between px-6 h-16">
        <!-- Left: Mobile Menu & Breadcrumb -->
        <div class="flex items-center gap-4">
            <button onclick="toggleSidebar()" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <div class="hidden sm:flex items-center gap-2 text-sm">
                <span class="text-gray-400"><i class="fas fa-warehouse"></i></span>
                <span class="text-gray-400">{{ $gare->nom_gare }}</span>
                <span class="text-gray-300">/</span>
                <span class="font-semibold text-gray-700">@yield('title', 'Dashboard')</span>
            </div>
        </div>

        <!-- Right: Profile -->
        <div class="flex items-center gap-4">

            <!-- Cloche notifications -->
            <div class="relative">
                <button id="gare-notif-btn" class="relative w-10 h-10 flex items-center justify-center bg-gray-100 hover:bg-orange-50 rounded-xl transition-all">
                    <i class="far fa-bell text-gray-600"></i>
                    @if($gare->unreadNotifications->count() > 0)
                        <span id="gare-notif-badge" class="absolute top-1.5 right-1.5 w-4 h-4 bg-[#e94f1b] text-white text-[9px] font-black flex items-center justify-center rounded-full">
                            {{ $gare->unreadNotifications->count() > 9 ? '9+' : $gare->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown notifications -->
                <div id="gare-notif-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 z-50">
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                        <p class="text-xs font-black text-gray-700 uppercase tracking-wider">Notifications</p>
                        @if($gare->unreadNotifications->count() > 0)
                        <form action="{{ route('gare-espace.notifications.markAllRead') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-[10px] font-bold text-orange-500 hover:underline">Tout marquer lu</button>
                        </form>
                        @endif
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                        @forelse($gare->notifications()->latest()->limit(10)->get() as $notif)
                        <a href="{{ route('gare-espace.notifications.go', $notif->id) }}"
                           class="block px-4 py-3 hover:bg-gray-50 transition-colors {{ $notif->read_at ? 'opacity-60' : 'bg-orange-50/40' }}">
                            <div class="flex gap-3">
                                <div class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $notif->read_at ? 'bg-gray-300' : 'bg-[#e94f1b]' }}"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-black text-gray-900 leading-tight truncate">{{ $notif->data['title'] ?? '' }}</p>
                                    <p class="text-[11px] font-medium text-gray-600 leading-snug mt-0.5">{{ $notif->data['message'] ?? '' }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 pt-1 uppercase">{{ $notif->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="px-4 py-8 text-center">
                            <i class="far fa-bell-slash text-gray-200 text-3xl mb-2 block"></i>
                            <p class="text-xs font-bold text-gray-400">Aucune notification</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Gare Info Badge -->
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-orange-50 rounded-lg border border-orange-200">
                <i class="fas fa-map-marker-alt text-orange-500 text-xs"></i>
                <span class="text-xs font-semibold text-orange-700">{{ $gare->ville }}</span>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="flex items-center gap-3 hover:bg-gray-50 rounded-xl px-3 py-2 transition-all">
                    @if($gare->profile_image)
                        <img src="{{ asset('storage/' . $gare->profile_image) }}" class="w-9 h-9 rounded-lg object-cover border border-gray-200" alt="Photo">
                    @else
                        <div class="w-9 h-9 bg-gradient-to-br from-orange-500 to-red-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($gare->responsable_prenom ?? 'G', 0, 1)) }}
                        </div>
                    @endif
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-gray-700 leading-none">{{ $gare->responsable_prenom }}</p>
                        <p class="text-xs text-gray-400">Responsable</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs hidden sm:block"></i>
                </button>

                <!-- Dropdown -->
                <div class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900">{{ $gare->responsable_prenom }} {{ $gare->responsable_nom }}</p>
                        <p class="text-xs text-gray-500">{{ $gare->email }}</p>
                    </div>
                    <a href="{{ route('gare-espace.profile') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-orange-50 hover:text-orange-600 transition-colors">
                        <i class="fas fa-user-circle"></i>
                        Mon Profil
                    </a>
                    <div class="border-t border-gray-50 mt-1"></div>
                    <form action="{{ route('gare-espace.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
<script>
(function() {
    const btn      = document.getElementById('gare-notif-btn');
    const dropdown = document.getElementById('gare-notif-dropdown');
    if (!btn || !dropdown) return;

    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
    });
    document.addEventListener('click', function() {
        dropdown.classList.add('hidden');
    });
    dropdown.addEventListener('click', function(e) { e.stopPropagation(); });
})();
</script>
