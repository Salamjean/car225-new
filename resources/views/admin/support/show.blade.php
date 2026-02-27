@extends('admin.layouts.template')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50/30 py-8 px-4">
    <div class="mx-auto" style="width: 92%">

        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.support.index') }}" class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-all shadow-sm">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">{{ $supportRequest->objet }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    @php
                        $typeBadges = [
                            'bagage_perdu' => ['label' => 'Bagage Perdu', 'color' => 'text-red-700', 'bg' => 'bg-red-50'],
                            'objet_oublie' => ['label' => 'Objet Oublié', 'color' => 'text-amber-700', 'bg' => 'bg-amber-50'],
                            'remboursement' => ['label' => 'Remboursement', 'color' => 'text-green-700', 'bg' => 'bg-green-50'],
                            'qualite' => ['label' => 'Qualité Service', 'color' => 'text-purple-700', 'bg' => 'bg-purple-50'],
                            'compte' => ['label' => 'Mon Compte', 'color' => 'text-sky-700', 'bg' => 'bg-sky-50'],
                            'autre' => ['label' => 'Autre', 'color' => 'text-gray-600', 'bg' => 'bg-gray-100'],
                        ];
                        $tb = $typeBadges[$supportRequest->type] ?? ['label' => $supportRequest->type, 'color' => 'text-gray-600', 'bg' => 'bg-gray-100'];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-1 {{ $tb['bg'] }} {{ $tb['color'] }} rounded-lg text-[10px] font-bold uppercase tracking-wider">{{ $tb['label'] }}</span>
                    <span class="text-xs text-gray-400"><i class="far fa-clock mr-1"></i>{{ $supportRequest->created_at->format('d/m/Y à H:i') }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-5 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <i class="fas fa-check-circle text-green-500"></i>
                <span class="font-semibold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne principale : Conversation --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 flex flex-col" style="height: calc(100vh - 200px); min-height: 500px;">

                    {{-- Zone de messages --}}
                    <div class="flex-1 overflow-y-auto p-6 space-y-5 bg-gray-50/50" id="chatBody">

                        {{-- Message initial --}}
                        <div class="flex gap-3 max-w-[85%]">
                            <div class="w-9 h-9 rounded-xl bg-gray-200 flex items-center justify-center text-gray-500 flex-shrink-0 overflow-hidden">
                                @if($supportRequest->user && $supportRequest->user->photo_profile_path)
                                    <img src="{{ asset('storage/' . $supportRequest->user->photo_profile_path) }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <i class="fas fa-user text-xs"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-[11px] font-bold text-gray-400 mb-1">{{ $supportRequest->user->name ?? 'Utilisateur' }}</p>
                                <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $supportRequest->description }}</p>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 ml-1">{{ $supportRequest->created_at->format('d/m/Y à H:i') }}</p>
                            </div>
                        </div>

                        {{-- Rétrocompatibilité --}}
                        @if($supportRequest->reponse && $supportRequest->messages->isEmpty())
                            <div class="flex gap-3 max-w-[85%] ml-auto flex-row-reverse">
                                <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white flex-shrink-0">
                                    <i class="fas fa-headset text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] font-bold text-gray-400 mb-1 text-right">Admin</p>
                                    <div class="bg-indigo-600 rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm">
                                        <p class="text-sm text-white leading-relaxed">{{ $supportRequest->reponse }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Messages --}}
                        @foreach($supportRequest->messages as $msg)
                            @if($msg->sender_type == 'user')
                                <div class="flex gap-3 max-w-[85%]">
                                    <div class="w-9 h-9 rounded-xl bg-gray-200 flex items-center justify-center text-gray-500 flex-shrink-0 overflow-hidden">
                                        @if($supportRequest->user && $supportRequest->user->photo_profile_path)
                                            <img src="{{ asset('storage/' . $supportRequest->user->photo_profile_path) }}" alt="" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-user text-xs"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-400 mb-1">{{ $supportRequest->user->name ?? 'Utilisateur' }}</p>
                                        <div class="bg-white border border-gray-200 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                                            <p class="text-sm text-gray-700 leading-relaxed">{{ $msg->message }}</p>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1 ml-1">{{ $msg->created_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex gap-3 max-w-[85%] ml-auto flex-row-reverse">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white flex-shrink-0">
                                        <i class="fas fa-headset text-xs"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-bold text-gray-400 mb-1 text-right">Admin</p>
                                        <div class="bg-indigo-600 rounded-2xl rounded-tr-sm px-4 py-3 shadow-sm">
                                            <p class="text-sm text-white leading-relaxed">{{ $msg->message }}</p>
                                        </div>
                                        <p class="text-[10px] text-gray-400 mt-1 text-right mr-1">{{ $msg->created_at->format('d/m/Y à H:i') }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Input de réponse --}}
                    @if($supportRequest->statut != 'ferme')
                        <div class="p-4 border-t border-gray-100 bg-white">
                            <form action="{{ route('admin.support.repondre', $supportRequest->id) }}" method="POST">
                                @csrf
                                <div class="flex items-end gap-3">
                                    <textarea name="reponse" rows="1" placeholder="Rédigez votre réponse..." required
                                        class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl text-sm font-medium outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all resize-none"
                                        style="max-height: 120px;" id="chatInput"></textarea>
                                    <button type="submit" class="w-11 h-11 bg-indigo-600 text-white rounded-xl flex items-center justify-center hover:bg-indigo-700 transition-all hover:shadow-lg hover:shadow-indigo-200 flex-shrink-0">
                                        <i class="fas fa-paper-plane text-sm"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="p-4 border-t border-gray-100 bg-gray-50 text-center">
                            <p class="text-sm text-gray-400 font-semibold"><i class="fas fa-lock mr-1"></i> Cette demande est fermée</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Colonne latérale : Infos --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Utilisateur --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h6 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">Utilisateur</h6>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 overflow-hidden flex-shrink-0">
                            @if($supportRequest->user && $supportRequest->user->photo_profile_path)
                                <img src="{{ asset('storage/' . $supportRequest->user->photo_profile_path) }}" alt="" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-user text-lg"></i>
                            @endif
                        </div>
                        <div>
                            @if($supportRequest->user)
                                <p class="text-sm font-bold text-gray-900">{{ $supportRequest->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $supportRequest->user->telephone }}</p>
                                <p class="text-xs text-gray-400">{{ $supportRequest->user->email }}</p>
                            @else
                                <p class="text-sm font-bold text-gray-900">Non inscrit</p>
                                <p class="text-xs text-gray-400">{{ $supportRequest->telephone ?? 'Non renseigné' }}</p>
                                <p class="text-xs text-gray-400">{{ $supportRequest->email ?? 'Non renseigné' }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Voyage concerné --}}
                @if($supportRequest->reservation)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h6 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">Voyage concerné</h6>
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="flex items-center gap-2 text-sm font-bold text-gray-900 mb-2">
                                <span>{{ $supportRequest->reservation->programme->itineraire->point_depart ?? '' }}</span>
                                <i class="fas fa-long-arrow-alt-right text-gray-300 text-xs"></i>
                                <span>{{ $supportRequest->reservation->programme->itineraire->point_arrive ?? '' }}</span>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-500"><i class="far fa-calendar mr-1.5 text-gray-400"></i>{{ \Carbon\Carbon::parse($supportRequest->reservation->date_voyage)->format('d/m/Y') }}</p>
                                <p class="text-xs text-gray-500"><i class="fas fa-barcode mr-1.5 text-gray-400"></i>{{ $supportRequest->reservation->reference }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Statut --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h6 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-3">Statut de la demande</h6>
                    <form action="{{ route('admin.support.statut', $supportRequest->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="statut" onchange="this.form.submit()"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold cursor-pointer outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="ouvert" {{ $supportRequest->statut == 'ouvert' ? 'selected' : '' }}>🟡 Ouvert / Nouveau</option>
                            <option value="en_cours" {{ $supportRequest->statut == 'en_cours' ? 'selected' : '' }}>🔵 En cours de traitement</option>
                            <option value="ferme" {{ $supportRequest->statut == 'ferme' ? 'selected' : '' }}>🟢 Fermé / Résolu</option>
                        </select>
                    </form>
                </div>

                {{-- Date --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h6 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Créé le</h6>
                    <p class="text-sm font-semibold text-gray-700">{{ $supportRequest->created_at->format('d/m/Y à H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll chat to bottom
    const chatBody = document.getElementById('chatBody');
    if (chatBody) chatBody.scrollTop = chatBody.scrollHeight;

    // Auto-resize textarea
    const input = document.getElementById('chatInput');
    if (input) {
        input.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });
    }
});
</script>
@endsection
