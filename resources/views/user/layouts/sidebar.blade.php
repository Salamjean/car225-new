<aside id="main-sidebar" class="fixed left-0 top-0 h-screen w-64 bg-white border-r border-gray-100 z-50 transition-transform duration-300 transform md:translate-x-0 -translate-x-full">
    <div class="flex flex-col h-full">
        <!-- Logo Section -->
        <div class="p-8">
            <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 flex items-center justify-center">
                    <img src="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <span class="text-xl font-black text-[#1A1D1F] italic tracking-tighter">CAR225</span>
            </a>
        </div>

        <!-- Navigation Section -->
        <div class="flex-1 overflow-y-auto px-4 py-4 space-y-8">
            <!-- Main Menu -->
            <div>
                <p class="px-4 text-[10px] font-bold text-gray-500/80 uppercase tracking-[0.2em] mb-4">Menu Principal</p>
                <nav class="space-y-1">
                    <a href="{{ route('user.dashboard') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('user.dashboard') ? 'bg-[#e94f1b]/5 text-[#e94f1b]' : 'text-[#1A1D1F]/80 hover:bg-gray-50 hover:text-[#1A1D1F]' }}">
                        <i class="fas fa-th-large text-sm {{ request()->routeIs('user.dashboard') ? 'text-[#e94f1b]' : 'group-hover:text-[#1A1D1F]' }}"></i>
                        <span class="text-sm font-bold tracking-tight">Tableau de bord</span>
                    </a>
                    <a href="{{ route('user.wallet.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('user.wallet.*') ? 'bg-[#e94f1b]/5 text-[#e94f1b]' : 'text-[#1A1D1F]/80 hover:bg-gray-50 hover:text-[#1A1D1F]' }}">
                        <i class="fas fa-wallet text-sm {{ request()->routeIs('user.wallet.*') ? 'text-[#e94f1b]' : 'group-hover:text-[#1A1D1F]' }}"></i>
                        <span class="text-sm font-bold tracking-tight">Portefeuille</span>
                    </a>
                    <a href="{{ route('reservation.create') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('reservation.create') ? 'bg-[#e94f1b]/5 text-[#e94f1b]' : 'text-[#1A1D1F]/80 hover:bg-gray-50 hover:text-[#1A1D1F]' }}">
                        <i class="fas fa-plus-circle text-sm {{ request()->routeIs('reservation.create') ? 'text-[#e94f1b]' : 'group-hover:text-[#1A1D1F]' }}"></i>
                        <span class="text-sm font-bold tracking-tight">Réserver un voyage</span>
                    </a>
                    <a href="{{ route('reservation.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('reservation.index') ? 'bg-[#e94f1b]/5 text-[#e94f1b]' : 'text-[#1A1D1F]/80 hover:bg-gray-50 hover:text-[#1A1D1F]' }}">
                        <i class="fas fa-ticket-alt text-sm {{ request()->routeIs('reservation.index') ? 'text-[#e94f1b]' : 'group-hover:text-[#1A1D1F]' }}"></i>
                        <span class="text-sm font-bold tracking-tight">Mes Billets</span>
                    </a>
                </nav>
            </div>

            <!-- Analysis & Incidents -->
            <div>
                <p class="px-4 text-[10px] font-bold text-gray-500/80 uppercase tracking-[0.2em] mb-4">Signalements</p>
                <nav class="space-y-1">
                    <a href="{{ route('signalement.create') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('signalement.create') ? 'bg-[#e94f1b]/5 text-[#e94f1b]' : 'text-[#1A1D1F]/80 hover:bg-gray-50 hover:text-[#1A1D1F]' }}">
                        <i class="fas fa-exclamation-triangle text-sm {{ request()->routeIs('signalement.create') ? 'text-[#e94f1b]' : 'group-hover:text-[#1A1D1F]' }}"></i>
                        <span class="text-sm font-bold tracking-tight">Déclarer incident</span>
                    </a>
                    <a href="{{ route('user.support.index') }}" class="flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all group {{ request()->routeIs('user.support.*') ? 'bg-[#e94f1b]/5 text-[#e94f1b]' : 'text-[#1A1D1F]/80 hover:bg-gray-50 hover:text-[#1A1D1F]' }}">
                        <i class="fas fa-headset text-sm {{ request()->routeIs('user.support.*') ? 'text-[#e94f1b]' : 'group-hover:text-[#1A1D1F]' }}"></i>
                        <span class="text-sm font-bold tracking-tight">Support Client</span>
                    </a>
                </nav>
            </div>


        </div>
    </div>
</aside>

<!-- Overlay for mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<style>
    @media (max-width: 768px) {
        #main-sidebar.active {
            transform: translateX(0);
        }
    }
</style>
