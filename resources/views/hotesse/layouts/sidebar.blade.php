<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: #231f20 !important; border-right: none !important; box-shadow: none !important;">
    <div class="mdc-drawer__header" style="padding: 30px 24px; background-color: #231f20 !important; margin: 0 !important; border-bottom: none !important;">
        <a href="{{ route('hotesse.dashboard') }}" class="brand-logo flex items-center gap-3 text-decoration-none">
            <div class="w-10 h-10 rounded-xl bg-black flex items-center justify-center shadow-lg shadow-black/20 p-1.5">
                <img src="{{asset('assetsPoster/assets/images/Car225_favicon.png')}}" class="w-full h-full object-contain" alt="Logo">
            </div>
            <div class="flex flex-col">
                <span class="text-white font-bold text-lg leading-tight tracking-tight">VoyageExpress</span>
                <span class="text-gray-400 text-[10px] font-bold uppercase tracking-wider opacity-60">Page hotesse</span>
            </div>
        </a>
    </div>
    <div class="mdc-drawer__content" style="background-color: #231f20 !important; padding-top: 0 !important;">
        <div class="mdc-list-group">
            <div class="px-6 py-6 text-[11px] font-bold text-gray-500 uppercase tracking-[2px]">NAVIGATION</div>
            <nav class="mdc-list mdc-drawer-menu px-3 space-y-3">
                <!-- Tableau de bord -->
                <div class="mdc-list-item mdc-drawer-item" style="height: auto; margin: 0; padding: 0;">
                    <a class="mdc-drawer-link group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('hotesse.dashboard') ? 'bg-[#e94e1a] !important; text-white !important;' : 'text-gray-300 hover:text-white' }}" 
                       href="{{ route('hotesse.dashboard') }}" style="{{ request()->routeIs('hotesse.dashboard') ? 'background-color: #e94e1a !important; color: white !important;' : 'color: #d1d5db;' }}">
                        <i class="fas fa-th-large mr-3 text-lg {{ request()->routeIs('hotesse.dashboard') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                        <span class="font-medium">Tableau de bord</span>
                    </a>
                </div>

                <!-- Vendre Ticket -->
                <div class="mdc-list-item mdc-drawer-item" style="height: auto; margin: 0; padding: 0;">
                    <a class="mdc-drawer-link group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('hotesse.vendre-ticket') ? 'bg-[#e94e1a] !important; text-white !important;' : 'text-gray-300 hover:text-white' }}" 
                       href="{{ route('hotesse.vendre-ticket') }}" style="{{ request()->routeIs('hotesse.vendre-ticket') ? 'background-color: #e94e1a !important; color: white !important;' : 'color: #d1d5db;' }}">
                        <i class="fas fa-plus-circle mr-3 text-lg {{ request()->routeIs('hotesse.vendre-ticket') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                        <span class="font-medium">Nouveau ticket</span>
                    </a>
                </div>

                <!-- Historique des Ventes -->
                <div class="mdc-list-item mdc-drawer-item" style="height: auto; margin: 0; padding: 0;">
                    <a class="mdc-drawer-link group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('hotesse.ventes') ? 'bg-[#e94e1a] !important; text-white !important;' : 'text-gray-300 hover:text-white' }}" 
                       href="{{ route('hotesse.ventes') }}" style="{{ request()->routeIs('hotesse.ventes') ? 'background-color: #e94e1a !important; color: white !important;' : 'color: #d1d5db;' }}">
                        <i class="fas fa-history mr-3 text-lg {{ request()->routeIs('hotesse.ventes') ? 'text-white' : 'text-gray-400 group-hover:text-white' }}"></i>
                        <span class="font-medium">Historique</span>
                    </a>
                </div>
            </nav>
        </div>
    </div>
</aside>
