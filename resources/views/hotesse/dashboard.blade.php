@extends('hotesse.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-orange-50 py-8 px-4">
    <div class="mx-auto" style="width: 90%">
        <!-- En-tête de bienvenue -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Bienvenue, {{ $hotesse->prenom }} ! 👋
                </h1>
                <div class="flex items-center gap-2">
                    <p class="text-lg font-semibold text-[#e94e1a]">
                        {{ $stats['compagnie'] }}
                    </p>
                    @if($stats['compagnie_slogan'])
                        <span class="text-gray-400">|</span>
                        <p class="text-gray-500 italic">{{ $stats['compagnie_slogan'] }}</p>
                    @endif
                </div>
            </div>
            @if($stats['compagnie_logo'])
                <div class="flex-shrink-0">
                    <img src="{{ asset('storage/' . $stats['compagnie_logo']) }}" alt="Logo" class="h-16 w-auto object-contain rounded-lg shadow-sm bg-white p-2">
                </div>
            @endif
        </div>

        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Tickets disponibles -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-[#e94e1a] hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Tickets Disponibles</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $stats['tickets_disponibles'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">Prêts à vendre</p>
                    </div>
                    <div class="w-16 h-16 bg-orange-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Compagnie -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-blue-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Ma Compagnie</p>
                        <p class="text-lg font-bold text-gray-900">{{ Str::limit($stats['compagnie'], 20) }}</p>
                        <p class="text-xs text-gray-500 mt-2">Affiliée</p>
                    </div>
                    <div class="w-16 h-16 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-green-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Statut du Compte</p>
                        <p class="text-lg font-bold text-green-600">Actif</p>
                        <p class="text-xs text-gray-500 mt-2">Opérationnel</p>
                    </div>
                    <div class="w-16 h-16 bg-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Ventes du jour -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-purple-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Ventes (Aujourd'hui)</p>
                        <p class="text-4xl font-bold text-gray-900">{{ $stats['ventes_aujourdhui'] }}</p>
                        <p class="text-xs text-gray-500 mt-2">Tickets vendus</p>
                    </div>
                    <div class="w-16 h-16 bg-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-1M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Revenu du jour -->
            <div class="bg-white rounded-2xl shadow-xl p-6 border-l-4 border-yellow-500 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600 mb-1">Revenu (Aujourd'hui)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['revenu_aujourdhui'], 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span></p>
                        <p class="text-xs text-gray-500 mt-2">Chiffre d'affaires</p>
                    </div>
                    <div class="w-16 h-16 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-3xl shadow-xl p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#e94e1a]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Actions Rapides
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Vendre des tickets -->
                <a href="{{ route('hotesse.vendre-ticket') }}" 
                   class="group p-6 bg-gradient-to-r from-[#e94e1a] to-[#d33d0f] rounded-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <h3 class="text-xl font-bold mb-2">Vendre des Tickets</h3>
                            <p class="text-orange-100">Créer une nouvelle vente</p>
                        </div>
                        <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                </a>

                <!-- Mon profil -->
                <a href="{{ route('hotesse.profile') }}" 
                   class="group p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="text-white">
                            <h3 class="text-xl font-bold mb-2">Mon Profil</h3>
                            <p class="text-blue-100">Gérer mes informations</p>
                        </div>
                        <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Informations importantes -->
        <div class="bg-gradient-to-r from-blue-50 to-orange-50 rounded-3xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Informations Importantes</h3>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Vous disposez de <strong>{{ $stats['tickets_disponibles'] }} ticket(s)</strong> à vendre</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Pour toute demande de rechargement, contactez votre compagnie</span>
                        </li>
                        @if($stats['tickets_disponibles'] < 10)
                        <li class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span class="text-yellow-700"><strong>Attention:</strong> Votre solde de tickets est bas. Pensez à demander un rechargement.</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#e94e1a',
            timer: 3000,
            timerProgressBar: true
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33'
        });
    @endif
</script>
@endsection
