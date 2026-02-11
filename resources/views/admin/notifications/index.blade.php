@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Centre de Notifications</h1>
            <p class="text-gray-600">Envoyez des messages aux utilisateurs sur le Web et Mobile</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Formulaire d'envoi -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6 text-white">
                        <div class="flex items-center gap-3">
                            <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-sm">
                                <i class="fas fa-paper-plane text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold">Nouvelle Notification</h2>
                                <p class="text-white/80 text-sm">Rédigez votre message ci-dessous</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.notifications.send') }}" method="POST" class="p-8 space-y-6">
                        @csrf
                        
                        <!-- Cibles -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Destination</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="target" value="all" checked class="peer hidden" onchange="toggleSpecificUser(false)">
                                        <div class="p-4 border-2 border-gray-100 rounded-2xl text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                            <i class="fas fa-users mb-2 block text-xl"></i>
                                            <span class="text-sm font-bold">Tous ({{ $usersCount }})</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="target" value="specific" class="peer hidden" onchange="toggleSpecificUser(true)">
                                        <div class="p-4 border-2 border-gray-100 rounded-2xl text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                            <i class="fas fa-user-tag mb-2 block text-xl"></i>
                                            <span class="text-sm font-bold">Spécifiques</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Canaux</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="channels[]" value="web" checked class="peer hidden">
                                        <div class="p-4 border-2 border-gray-100 rounded-2xl text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                            <i class="fas fa-globe mb-2 block text-xl"></i>
                                            <span class="text-sm font-bold">Web</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="channels[]" value="mobile" checked class="peer hidden">
                                        <div class="p-4 border-2 border-gray-100 rounded-2xl text-center peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                            <i class="fas fa-mobile-alt mb-2 block text-xl"></i>
                                            <span class="text-sm font-bold">Mobile</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Sélection Utilisateurs (Masqué par défaut) -->
                        <div id="specific-user-container" class="hidden space-y-2 animate-fadeIn">
                            <label class="block text-sm font-bold text-gray-700">Sélectionner les utilisateurs</label>
                            <div class="relative">
                                <input type="text" id="user-search" placeholder="Rechercher par nom ou email..." 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <div id="search-results" class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 hidden">
                                    <!-- Résultats via JS -->
                                </div>
                            </div>
                            <div id="selected-users" class="flex flex-wrap gap-2 pt-2">
                                <!-- Users sélectionnés via JS -->
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div class="md:col-span-3 space-y-2">
                                    <label class="block text-sm font-bold text-gray-700">Titre de la notification</label>
                                    <input type="text" name="title" required placeholder="Ex: Maintenance système prévue" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-bold text-gray-700">Type</label>
                                    <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                        <option value="info">Information (Bleu)</option>
                                        <option value="success">Succès (Vert)</option>
                                        <option value="warning">Avertissement (Orange)</option>
                                        <option value="error">Urgent (Rouge)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Message</label>
                                <textarea name="message" rows="4" required placeholder="Votre message ici..." 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>

                        <!-- Bouton -->
                        <div class="pt-4">
                            <button type="submit" class="w-full py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-lg transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                Envoyer la notification maintenant
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Aide & Stats -->
            <div class="space-y-8">
                <div class="bg-white rounded-3xl shadow-lg p-8">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 border-b pb-4">Conseils</h3>
                    <ul class="space-y-4">
                        <li class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">1</div>
                            <p class="text-sm text-gray-600">Restez concis pour les notifications mobiles.</p>
                        </li>
                        <li class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">2</div>
                            <p class="text-sm text-gray-600">Utilisez le type **Urgent** uniquement pour les pannes importantes.</p>
                        </li>
                        <li class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">3</div>
                            <p class="text-sm text-gray-600">Les notifications Web apparaîtront au prochain rafraîchissement des utilisateurs.</p>
                        </li>
                    </ul>
                </div>

                <div class="bg-gradient-to-br from-[#e94f1b] to-orange-500 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold mb-2">Impact estimé</h3>
                        <div class="text-4xl font-black mb-1">{{ $usersCount }}</div>
                        <p class="text-white/80 text-sm">Destinataires potentiels</p>
                    </div>
                    <i class="fas fa-broadcast-tower absolute -right-4 -bottom-4 text-white/10 text-9xl transform -rotate-12"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const searchInput = document.getElementById('user-search');
    const resultsDiv = document.getElementById('search-results');
    const selectedDiv = document.getElementById('selected-users');
    let selectedUsers = [];

    function toggleSpecificUser(show) {
        const container = document.getElementById('specific-user-container');
        if (show) {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value;
            if (query.length < 2) {
                resultsDiv.classList.add('hidden');
                return;
            }

            fetch(`{{ route('admin.notifications.search') }}?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(user => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-0';
                            div.innerHTML = `
                                <div class="font-bold text-gray-900">${user.name} ${user.prenom}</div>
                                <div class="text-xs text-gray-500">${user.email}</div>
                            `;
                            div.onclick = () => selectUser(user);
                            resultsDiv.appendChild(div);
                        });
                        resultsDiv.classList.remove('hidden');
                    } else {
                        resultsDiv.innerHTML = '<div class="px-4 py-3 text-gray-500 italic">Aucun utilisateur trouvé</div>';
                        resultsDiv.classList.remove('hidden');
                    }
                });
        });

        // Fermer les résultats si on clique ailleurs
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
                resultsDiv.classList.add('hidden');
            }
        });
    }

    function selectUser(user) {
        if (selectedUsers.some(u => u.id === user.id)) return;
        
        selectedUsers.push(user);
        renderSelectedUsers();
        resultsDiv.classList.add('hidden');
        searchInput.value = '';
    }

    function removeUser(userId) {
        selectedUsers = selectedUsers.filter(u => u.id !== userId);
        renderSelectedUsers();
    }

    function renderSelectedUsers() {
        selectedDiv.innerHTML = '';
        selectedUsers.forEach(user => {
            const badge = document.createElement('div');
            badge.className = 'flex items-center gap-2 bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-xs font-bold';
            badge.innerHTML = `
                ${user.name} ${user.prenom}
                <input type="hidden" name="user_ids[]" value="${user.id}">
                <button type="button" onclick="removeUser(${user.id})" class="hover:text-red-500">
                    <i class="fas fa-times"></i>
                </button>
            `;
            selectedDiv.appendChild(badge);
        });
    }

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Envoyé !', text: '{{ session('success') }}', confirmButtonColor: '#2563eb' });
    @endif
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endsection
