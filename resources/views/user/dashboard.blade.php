@extends('user.layouts.template')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto" style="width: 95%;">

            <!-- Welcome Header -->
            <div class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div
                        class="w-16 h-16 bg-gradient-to-tr from-green-600 to-green-400 rounded-2xl shadow-lg flex items-center justify-center text-white text-2xl font-black">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Bonjour, {{ $user->name }} !</h1>
                        <p class="text-gray-500 font-medium">Heureux de vous revoir. Où allez-vous aujourd'hui ?</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('reservation.create') }}"
                        class="px-6 py-3 bg-green-600 text-white rounded-xl shadow-xl hover:bg-green-700 transition-all font-bold flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nouvelle Réservation
                    </a>
                </div>
            </div>

            <!-- Progress/Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <!-- Active Trips -->
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bus text-6xl text-green-600"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Voyages à venir</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $activeReservations }}</h3>
                    <div
                        class="mt-4 flex items-center text-xs font-bold text-green-600 bg-green-50 w-fit px-2 py-1 rounded-lg">
                        Prochains départs
                    </div>
                </div>

                <!-- Total Reservations -->
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fas fa-history text-6xl text-purple-600"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Réservations</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $totalReservations }}</h3>
                    <div
                        class="mt-4 flex items-center text-xs font-bold text-purple-600 bg-purple-50 w-fit px-2 py-1 rounded-lg">
                        Historique complet
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fas fa-wallet text-6xl text-green-600"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Dépensé</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ number_format($totalSpent, 0, ',', ' ') }} <span
                            class="text-sm">CFA</span></h3>
                    <div
                        class="mt-4 flex items-center text-xs font-bold text-green-600 bg-green-50 w-fit px-2 py-1 rounded-lg">
                        Economies & Voyages
                    </div>
                </div>

                <!-- Signalements -->
                <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:scale-110 transition-transform">
                        <i class="fas fa-bullhorn text-6xl text-red-600"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Signalements</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ $totalSignalements }}</h3>
                    <div class="mt-4 flex items-center text-xs font-bold text-red-600 bg-red-50 w-fit px-2 py-1 rounded-lg">
                        Incidents rapportés
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Recent Bookings -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between px-2">
                        <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Mes derniers billets</h2>
                        <a href="{{ route('reservation.index') }}"
                            class="text-sm font-bold text-green-600 hover:underline">Voir tout l'historique</a>
                    </div>

                    <div class="space-y-4">
                        @forelse($recentReservations as $res)
                            <div
                                class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-2xl transition-all group">
                                <div class="flex items-center gap-5">
                                    <div
                                        class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center border border-gray-100 group-hover:bg-green-50 transition-colors">
                                        @if($res->programme->compagnie->path_logo)
                                            <img src="{{ asset('storage/' . $res->programme->compagnie->path_logo) }}"
                                                class="w-10 h-10 object-contain" alt="Logo">
                                        @else
                                            <i class="fas fa-bus text-green-600 text-xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <span
                                                class="text-[10px] font-black text-green-600 uppercase tracking-widest">{{ $res->programme->compagnie->name }}</span>
                                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                            <span
                                                class="text-[10px] font-bold text-gray-400 capitalize">{{ $res->is_aller_retour ? 'Aller-Retour' : 'Aller simple' }}</span>
                                        </div>
                                        <h4 class="font-black text-gray-900 text-lg">
                                            {{ $res->programme->point_depart }} <i
                                                class="fas fa-arrow-right text-xs mx-1 text-gray-300"></i>
                                            {{ $res->programme->point_arrive }}
                                        </h4>
                                        <p class="text-xs font-bold text-gray-500 mt-1">
                                            <i class="far fa-calendar-alt mr-1"></i>
                                            {{ \Carbon\Carbon::parse($res->date_voyage)->format('d M Y') }}
                                            <span class="mx-2 text-gray-300">|</span>
                                            <i class="fas fa-chair mr-1"></i> {{ $res->nombre_places }} places
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-4 border-t md:border-t-0 pt-4 md:pt-0">
                                    <div class="text-right">
                                        <p class="text-xs font-black text-gray-400 uppercase">Montant</p>
                                        <p class="text-xl font-black text-gray-900">
                                            {{ number_format($res->montant_total, 0, ',', ' ') }} <span
                                                class="text-xs">CFA</span></p>
                                    </div>
                                    <a href="{{ route('reservations.show', $res->id) }}"
                                        class="w-12 h-12 bg-gray-900 text-white rounded-2xl flex items-center justify-center hover:bg-green-600 transition-all shadow-lg">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-3xl p-12 text-center border-2 border-dashed border-gray-200">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-ticket-alt text-3xl text-gray-300"></i>
                                </div>
                                <h3 class="text-lg font-black text-gray-700">Aucun billet trouvé</h3>
                                <p class="text-gray-400 mt-1 text-sm">C'est le moment de planifier votre prochain voyage !</p>
                                <a href="{{ route('reservation.create') }}"
                                    class="mt-6 inline-block px-8 py-3 bg-green-600 text-white rounded-xl font-bold shadow-lg hover:bg-green-700 transition-all">
                                    Réserver maintenant
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-8">
                    <!-- Recent Signalements -->
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                        <div
                            class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gradient-to-r from-red-50 to-white">
                            <h3 class="text-sm font-black text-red-800 uppercase tracking-widest">Signalements</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($recentSignalements as $sig)
                                <div
                                    class="p-4 bg-gray-50 rounded-2xl border border-gray-100 hover:border-red-200 transition-all">
                                    <div class="flex justify-between items-start mb-2">
                                        <span
                                            class="px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black uppercase rounded">{{ $sig->type }}</span>
                                        <span
                                            class="text-[9px] text-gray-400 font-bold uppercase">{{ $sig->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-[11px] text-gray-600 line-clamp-2 leading-relaxed italic">
                                        "{{ $sig->description }}"</p>
                                    <div class="mt-2 flex items-center gap-1">
                                        <i class="fas fa-bus text-[9px] text-gray-300"></i>
                                        <span
                                            class="text-[9px] font-bold text-gray-500">{{ $sig->programme->compagnie->name ?? 'Compagnie' }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6">
                                    <i class="fas fa-shield-alt text-2xl text-green-400 opacity-20 mb-2"></i>
                                    <p class="text-[10px] font-black text-gray-400 uppercase">Tout est en ordre</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Ad/Banner Section -->
                    <div
                        class="bg-gradient-to-br from-green-700 to-green-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl">
                        <div class="relative z-10">
                            <h3 class="text-xl font-black mb-2 leading-tight">Gagnez du temps avec CAR225</h3>
                            <p class="text-green-100 text-sm mb-6 leading-relaxed">Réservez vos billets en quelques clics et
                                évitez les files d'attente en gare.</p>
                            <a href="{{ route('reservation.create') }}"
                                class="inline-block px-5 py-2.5 bg-white text-green-900 rounded-xl font-black text-xs shadow-lg hover:scale-105 transition-transform">
                                EXPLORER LES TRAJETS
                            </a>
                        </div>
                        <!-- Decorative shapes -->
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                        <div class="absolute -top-10 -left-10 w-40 h-40 bg-green-400/20 rounded-full blur-2xl"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Premium scrollbar if content overflows */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
@endsection