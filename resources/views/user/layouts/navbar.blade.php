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
            <div class="relative mdc-menu-surface--anchor">
                <button id="notif-dropdown-btn" class="relative w-11 h-11 flex items-center justify-center bg-white/10 hover:bg-white/20 rounded-xl transition-all group">
                    <i class="far fa-bell text-white group-hover:scale-110 transition-all"></i>
                    @if($user->unreadNotifications->count() > 0)
                        <span id="notif-badge" class="absolute top-3 right-3 w-4 h-4 bg-white text-[#e94f1b] text-[10px] font-black flex items-center justify-center rounded-full border border-[#e94f1b]">
                            {{ $user->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>

                <!-- Dropdown des Notifications -->
                <div id="notif-dropdown" class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200 z-50">
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                        <span class="font-black text-gray-900 text-sm">NOTIFICATIONS</span>
                        <a href="#" id="mark-all-read" class="text-[10px] font-bold text-[#e94f1b] hover:underline uppercase">Tout lire</a>
                    </div>
                    <div class="max-h-80 overflow-y-auto scrollbar-thin">
                        @forelse($user->notifications()->limit(10)->get() as $notification)
                            <div class="px-4 py-4 border-b border-gray-50 hover:bg-gray-50 transition-colors {{ $notification->read_at ? 'opacity-60' : 'bg-blue-50/30' }}">
                                <div class="flex gap-3">
                                    <div class="w-2 h-2 rounded-full mt-1.5 shrink-0 {{ $notification->read_at ? 'bg-gray-300' : 'bg-[#e94f1b]' }}"></div>
                                    <div class="space-y-1">
                                        <p class="text-xs font-black text-gray-900 leading-tight">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                        <p class="text-[11px] font-medium text-gray-600 leading-snug">{{ $notification->data['message'] ?? '' }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 pt-1 uppercase">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center">
                                <i class="far fa-bell-slash text-gray-200 text-3xl mb-2"></i>
                                <p class="text-xs font-bold text-gray-400">Aucune notification</p>
                            </div>
                        @endforelse
                    </div>
                    @if($user->notifications->count() > 0)
                        <div class="px-4 py-2 border-t border-gray-100 text-center">
                            <a href="#" class="text-[10px] font-black text-gray-400 hover:text-[#e94f1b] transition-colors uppercase">Voir tout</a>
                        </div>
                    @endif
                </div>
            </div>

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
                    <div id="profile-dropdown" class="absolute right-0 mt-3 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 hidden animate-in fade-in slide-in-from-top-2 duration-200 z-50">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                if (notifDropdown) notifDropdown.classList.add('hidden');
            });
        }

        // Notification Dropdown
        const notifBtn = document.getElementById('notif-dropdown-btn');
        const notifDropdown = document.getElementById('notif-dropdown');
        const markAllRead = document.getElementById('mark-all-read');

        if(notifBtn && notifDropdown) {
            notifBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                notifDropdown.classList.toggle('hidden');
                if (profileDropdown) profileDropdown.classList.add('hidden');
            });
        }

        if(markAllRead) {
            markAllRead.addEventListener('click', (e) => {
                e.preventDefault();
                fetch('{{ route('user.notifications.mark-all-read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).then(() => window.location.reload());
            });
        }

        document.addEventListener('click', (e) => {
            if(profileDropdown && !profileDropdown.contains(e.target)) profileDropdown.classList.add('hidden');
            if(notifDropdown && !notifDropdown.contains(e.target)) notifDropdown.classList.add('hidden');
        });

        // Affichage auto des notifications non lues au chargement
        @php
            $unreadNotifs = $user->unreadNotifications;
        @endphp

        @if($unreadNotifs->count() > 0)
            const unreadNotifications = @json($unreadNotifs);
            
            async function showNotificationsSequentially() {
                for (const notif of unreadNotifications) {
                    const iconType = notif.data.type || 'info';
                    const icon = iconType === 'error' ? 'error' : (iconType === 'warning' ? 'warning' : (iconType === 'success' ? 'success' : 'info'));
                    
                    await Swal.fire({
                        title: notif.data.title || 'Notification',
                        text: notif.data.message || '',
                        icon: icon,
                        confirmButtonText: 'Fermer',
                        confirmButtonColor: '#e94f1b',
                        toast: false,
                        position: 'center',
                        allowOutsideClick: false,
                        customClass: {
                            popup: 'rounded-3xl border-4 border-[#e94f1b]/10 shadow-2xl'
                        }
                    });

                    // Marquer comme lue individuellement
                    fetch('{{ route('user.notifications.mark-read') }}', {
                        method: 'POST',
                        body: JSON.stringify({ id: notif.id }),
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });
                }
                // Optionnel: recharger pour mettre à jour le badge sans rafraîchissement manuel
                // window.location.reload(); 
            }

            // Un petit délai pour pas agresser l'utilisateur direct
            setTimeout(showNotificationsSequentially, 1500);
        @endif

        // --- Laravel Reverb : Listen for Real-time notifications ---
        if (window.Echo) {
            window.Echo.private('App.Models.User.{{ $user->id }}')
                .notification((notification) => {
                    console.log('Real-time notification received:', notification);
                    
                    // 1. Update Badge
                    const badge = document.getElementById('notif-badge');
                    if (badge) {
                        badge.textContent = notification.count;
                    } else {
                        // Create badge if it doesn't exist
                        const btn = document.getElementById('notif-dropdown-btn');
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notif-badge';
                        newBadge.className = 'absolute top-3 right-3 w-4 h-4 bg-white text-[#e94f1b] text-[10px] font-black flex items-center justify-center rounded-full border border-[#e94f1b]';
                        newBadge.textContent = notification.count;
                        btn.appendChild(newBadge);
                    }

                    // 2. Show SweetAlert
                    Swal.fire({
                        title: notification.title || 'Notification',
                        text: notification.message || '',
                        icon: notification.type === 'error' ? 'error' : (notification.type === 'warning' ? 'warning' : (notification.type === 'success' ? 'success' : 'info')),
                        confirmButtonText: 'Fermer',
                        confirmButtonColor: '#e94f1b',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'rounded-2xl border-2 border-[#e94f1b]/10 shadow-xl'
                        }
                    });
                    
                    // 3. Mark as read on backend (optional, or wait for user to click)
                    // We don't mark as read automatically in real-time to allow user to see it in history later
                });
        }
    });
</script>