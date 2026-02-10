<header class="fixed top-0 right-0 left-0 md:left-64 z-40 px-4 sm:px-8 py-4 shadow-lg shadow-[#e94f1b]/10" style="background-color: #e94f1b !important;">
    @php
        $user = Auth::guard('web')->user();
        $initials = strtoupper(substr($user->name, 0, 1) . substr($user->prenom, 0, 1));
    @endphp
    <div class="flex items-center justify-between gap-4">
        <!-- Mobile Toggle -->
        <button id="sidebar-toggle" class="md:hidden w-11 h-11 flex items-center justify-center bg-white/10 rounded-xl text-white">
            <i class="fas fa-bars"></i>
        </button>

        <div class="flex-1"></div>

        <!-- Right Side Actions -->
        <div class="flex items-center gap-3 sm:gap-6">
            <!-- Notifications -->
            <button class="relative w-11 h-11 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-xl transition-all group">
                <i class="far fa-bell text-white group-hover:scale-110 transition-all"></i>
                <span class="absolute top-3 right-3 w-2 h-2 bg-white rounded-full border-2 border-[#e94f1b]"></span>
            </button>

            <!-- User Profile & Logout Group -->
            <div class="flex items-center gap-3 pl-3 sm:pl-6 border-l border-white/20 h-11">
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-black text-white tracking-tight leading-none mb-1 uppercase">{{ $user->name }}</p>
                    <p class="text-[10px] font-bold text-white/70 uppercase tracking-widest leading-none">Voyageur</p>
                </div>
                <div class="mdc-menu-surface--anchor relative">
                    <button id="profile-dropdown-btn" class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-[#e94f1b] font-black overflow-hidden shadow-lg transition-transform hover:scale-105 active:scale-95">
                        @if($user->photo_profile_path)
                            <img src="{{ asset('storage/' . $user->photo_profile_path) }}" alt="Profile" class="w-full h-full object-cover">
                        @else
                            {{ $initials }}
                        @endif
                    </button>
                    <!-- Dropdown Menu -->
                    <div id="profile-dropdown" class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200">
                        <a href="{{ route('user.profile') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user-circle text-[#e94f1b]"></i> Mon Profil
                        </a>
                    </div>
                </div>

                <!-- Logout Icon -->
                <a href="{{ route('user.logout') }}" class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-xl transition-all group text-white ms-2" title="Déconnexion">
                    <i class="fas fa-power-off text-sm group-hover:scale-110 transition-all"></i>
                </a>
            </div>
        </div>
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sidebar Toggle
        const toggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('main-sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if(toggle && sidebar && overlay) {
            toggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            });

            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }

        // Profile Dropdown
        const profileBtn = document.getElementById('profile-dropdown-btn');
        const profileDropdown = document.getElementById('profile-dropdown');

        if(profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if(!profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>