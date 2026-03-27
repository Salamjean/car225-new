@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50/30 py-8 px-4">
    <div class="mx-auto" style="width: 100%">

        {{-- En-tête --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-1">Support Client</h1>
                <p class="text-gray-500 text-sm">Gestion des préoccupations et signalements des utilisateurs</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Statistiques --}}
        @php
            $totalRequests = $requests->total();
            $openCount = \App\Models\SupportRequest::where('statut', 'ouvert')->count();
            $enCoursCount = \App\Models\SupportRequest::where('statut', 'en_cours')->count();
            $fermeCount = \App\Models\SupportRequest::where('statut', 'ferme')->count();
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <i class="fas fa-headset text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-gray-900">{{ $totalRequests }}</p>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total demandes</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition-colors">
                        <i class="fas fa-envelope-open-text text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-gray-900">{{ $openCount }}</p>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ouvertes</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fas fa-spinner text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-gray-900">{{ $enCoursCount }}</p>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">En cours</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors">
                        <i class="fas fa-check-double text-lg"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-gray-900">{{ $fermeCount }}</p>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Résolues</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Carte principale --}}
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">

            {{-- En-tête + Filtres --}}
            <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-4">
                    <div class="flex items-center">
                        <div class="w-2 h-8 bg-indigo-600 rounded-full mr-3"></div>
                        <h2 class="text-lg font-bold text-gray-800">Liste des demandes</h2>
                        <span class="ml-3 px-3 py-1 bg-indigo-600 text-white text-xs font-bold rounded-full">
                            {{ $totalRequests }} demande(s)
                        </span>
                    </div>
                </div>

                {{-- Filtres --}}
                <form method="GET" action="{{ route('admin.support.index') }}">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Rechercher</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, objet, email..."
                                class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Catégorie</label>
                            <select name="type" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">
                                <option value="">Toutes</option>
                                <option value="bagage_perdu" {{ request('type') == 'bagage_perdu' ? 'selected' : '' }}>Bagage Perdu</option>
                                <option value="objet_oublie" {{ request('type') == 'objet_oublie' ? 'selected' : '' }}>Objet Oublié</option>
                                <option value="remboursement" {{ request('type') == 'remboursement' ? 'selected' : '' }}>Remboursement</option>
                                <option value="qualite" {{ request('type') == 'qualite' ? 'selected' : '' }}>Qualité Service</option>
                                <option value="compte" {{ request('type') == 'compte' ? 'selected' : '' }}>Mon Compte</option>
                                <option value="autre" {{ request('type') == 'autre' ? 'selected' : '' }}>Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Statut</label>
                            <select name="statut" class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition-all">
                                <option value="">Tous</option>
                                <option value="ouvert" {{ request('statut') == 'ouvert' ? 'selected' : '' }}>Ouvert</option>
                                <option value="en_cours" {{ request('statut') == 'en_cours' ? 'selected' : '' }}>En cours</option>
                                <option value="ferme" {{ request('statut') == 'ferme' ? 'selected' : '' }}>Fermé</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 shadow-lg shadow-indigo-200">
                                <i class="fas fa-search"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.support.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 transition-colors flex items-center justify-center" title="Réinitialiser">
                                <i class="fas fa-redo text-xs"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tableau --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Catégorie</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Objet</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Messages</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-50">
                        @forelse($requests as $request)
                            @php
                                $badges = [
                                    'bagage_perdu' => ['label' => 'Bagage Perdu', 'color' => 'text-red-700', 'bg' => 'bg-red-50', 'icon' => 'fa-suitcase-rolling'],
                                    'objet_oublie' => ['label' => 'Objet Oublié', 'color' => 'text-amber-700', 'bg' => 'bg-amber-50', 'icon' => 'fa-glasses'],
                                    'remboursement' => ['label' => 'Remboursement', 'color' => 'text-green-700', 'bg' => 'bg-green-50', 'icon' => 'fa-hand-holding-usd'],
                                    'qualite' => ['label' => 'Qualité Service', 'color' => 'text-purple-700', 'bg' => 'bg-purple-50', 'icon' => 'fa-star'],
                                    'compte' => ['label' => 'Mon Compte', 'color' => 'text-sky-700', 'bg' => 'bg-sky-50', 'icon' => 'fa-user-cog'],
                                    'autre' => ['label' => 'Autre', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50', 'icon' => 'fa-question-circle'],
                                ];
                                $badge = $badges[$request->type] ?? ['label' => $request->type, 'color' => 'text-gray-600', 'bg' => 'bg-gray-50', 'icon' => 'fa-circle'];
                                $msgCount = $request->messages->count();
                            @endphp
                            <tr class="hover:bg-indigo-50/30 transition-colors duration-200 {{ $request->statut == 'ouvert' ? 'border-l-4 border-l-amber-400' : '' }}">
                                {{-- Utilisateur --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 overflow-hidden flex-shrink-0">
                                            @if($request->user && $request->user->photo_profile_path)
                                                <img src="{{ asset('storage/' . $request->user->photo_profile_path) }}" alt="" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-user text-sm"></i>
                                            @endif
                                        </div>
                                        <div>
                                            @if($request->user)
                                                <p class="text-sm font-bold text-gray-900">{{ $request->user->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $request->user->telephone ?? $request->user->email }}</p>
                                            @else
                                                <p class="text-sm font-bold text-gray-900">Non inscrit</p>
                                                <p class="text-xs text-gray-400">{{ $request->telephone ?? $request->email ?? '-' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Catégorie --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $badge['bg'] }} {{ $badge['color'] }} rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                        <i class="fas {{ $badge['icon'] }}"></i>
                                        {{ $badge['label'] }}
                                    </span>
                                </td>

                                {{-- Objet --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-900 truncate max-w-[200px]">{{ $request->objet }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ Str::limit($request->description, 50) }}</p>
                                </td>

                                {{-- Messages --}}
                                <td class="px-6 py-4 text-center">
                                    @if($msgCount > 0)
                                        <span class="inline-flex items-center justify-center w-7 h-7 bg-indigo-600 text-white text-xs font-bold rounded-full">{{ $msgCount }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs font-bold">0</span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-700">{{ $request->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $request->created_at->format('H:i') }}</p>
                                </td>

                                {{-- Statut --}}
                                <td class="px-6 py-4 text-center">
                                    @if($request->statut == 'ouvert')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Nouveau
                                        </span>
                                    @elseif($request->statut == 'en_cours')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span> En cours
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 text-green-700 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Résolu
                                        </span>
                                    @endif
                                </td>

                                {{-- Action --}}
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.support.show', $request->id) }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold hover:bg-indigo-700 transition-all hover:shadow-lg hover:shadow-indigo-200 hover:-translate-y-0.5">
                                        <i class="fas fa-eye"></i> Consulter
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center text-gray-300 mb-4">
                                            <i class="fas fa-inbox text-3xl"></i>
                                        </div>
                                        <p class="text-gray-400 font-semibold">Aucune demande au support pour le moment.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Affichage de {{ $requests->firstItem() }} à {{ $requests->lastItem() }} sur {{ $requests->total() }} résultats
                        </div>
                        <div>{{ $requests->withQueryString()->links() }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .pagination { display: flex; list-style: none; padding: 0; margin: 0; gap: 4px; }
    .page-item .page-link { padding: 6px 12px; border: 1px solid #e5e7eb; border-radius: 10px; color: #6b7280; text-decoration: none; font-size: 13px; font-weight: 600; transition: all 0.2s; }
    .page-item.active .page-link { background: #4f46e5; border-color: #4f46e5; color: #fff; }
    .page-item .page-link:hover { background: #f3f4f6; }
    .page-item.active .page-link:hover { background: #4338ca; }
</style>
@endsection
