@extends('user.layouts.template')

@section('content')
<div class="min-h-screen bg-[#F8F9FA] py-8 px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-[1200px]">

        {{-- Header --}}
        <div class="mb-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('user.support.index') }}"
                   class="w-10 h-10 bg-white border border-gray-200 rounded-2xl flex items-center justify-center text-gray-500 hover:bg-[#e94f1b] hover:text-white hover:border-[#e94f1b] transition-all shadow-sm">
                    <i class="fas fa-arrow-left text-sm"></i>
                </a>
                <div>
                    <h1 class="text-xl sm:text-3xl font-black text-[#1A1D1F] tracking-tight">Mes Déclarations</h1>
                    <p class="text-gray-500 font-medium">Suivez l'état de vos demandes et les réponses reçues</p>
                </div>
            </div>
            <a href="{{ route('user.support.index') }}"
               class="inline-flex items-center gap-2 bg-[#e94f1b] text-white px-5 py-3 rounded-2xl font-bold text-sm hover:bg-[#c73d12] transition-all shadow-md hover:shadow-[0_8px_24px_rgba(233,79,27,0.35)]">
                <i class="fas fa-plus text-xs"></i>
                Nouvelle déclaration
            </a>
        </div>

        @php
            $totalDeclarations = $declarations->flatten()->count();
            $hasReponse        = $declarations->flatten()->whereNotNull('reponse')->count();
            $enCours           = $declarations->flatten()->where('statut', 'en_cours')->count();
            $fermes            = $declarations->flatten()->where('statut', 'ferme')->count();
        @endphp

        {{-- Stats rapides --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
            <div class="bg-white rounded-[24px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-[#1A1D1F] rounded-2xl flex items-center justify-center text-white flex-shrink-0">
                    <i class="fas fa-inbox text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-[#1A1D1F]">{{ $totalDeclarations }}</div>
                    <div class="text-xs text-gray-500 font-medium">Total</div>
                </div>
            </div>
            <div class="bg-white rounded-[24px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 flex-shrink-0">
                    <i class="fas fa-clock text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-[#1A1D1F]">{{ $enCours }}</div>
                    <div class="text-xs text-gray-500 font-medium">En cours</div>
                </div>
            </div>
            <div class="bg-white rounded-[24px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-green-500 flex-shrink-0">
                    <i class="fas fa-comment-dots text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-[#1A1D1F]">{{ $hasReponse }}</div>
                    <div class="text-xs text-gray-500 font-medium">Avec réponse</div>
                </div>
            </div>
            <div class="bg-white rounded-[24px] p-5 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 flex-shrink-0">
                    <i class="fas fa-check-double text-lg"></i>
                </div>
                <div>
                    <div class="text-2xl font-black text-[#1A1D1F]">{{ $fermes }}</div>
                    <div class="text-xs text-gray-500 font-medium">Traitées</div>
                </div>
            </div>
        </div>

        @if($totalDeclarations === 0)
            {{-- État vide --}}
            <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm p-16 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Aucune déclaration</h3>
                <p class="text-gray-500 text-sm mb-8">Vous n'avez encore effectué aucune demande de support.</p>
                <a href="{{ route('user.support.index') }}"
                   class="inline-flex items-center gap-2 bg-[#e94f1b] text-white px-6 py-3 rounded-2xl font-bold text-sm hover:bg-[#c73d12] transition-all">
                    <i class="fas fa-plus text-xs"></i>
                    Faire une déclaration
                </a>
            </div>
        @else
            {{-- Onglets par type --}}
            <div class="mb-6 flex flex-wrap gap-2" id="typeTabs">
                <button onclick="filterType('all')" data-tab="all"
                        class="tab-btn active-tab px-5 py-2.5 rounded-2xl font-bold text-sm transition-all">
                    Tous ({{ $totalDeclarations }})
                </button>
                @foreach($declarations as $type => $items)
                    @php $meta = $typesLabels[$type] ?? ['label' => ucfirst($type), 'icon' => 'fa-circle', 'color' => 'gray']; @endphp
                    <button onclick="filterType('{{ $type }}')" data-tab="{{ $type }}"
                            class="tab-btn px-5 py-2.5 rounded-2xl font-bold text-sm transition-all">
                        <i class="fas {{ $meta['icon'] }} mr-1.5"></i>
                        {{ $meta['label'] }} ({{ $items->count() }})
                    </button>
                @endforeach
            </div>

            {{-- Liste des déclarations --}}
            <div class="space-y-6" id="declarationsList">
                @foreach($declarations as $type => $items)
                    @php $meta = $typesLabels[$type] ?? ['label' => ucfirst($type), 'icon' => 'fa-circle', 'color' => 'gray']; @endphp

                    @php
                        $colorMap = [
                            'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-[#e94f1b]', 'border' => 'border-orange-100', 'badge' => 'bg-orange-100 text-[#e94f1b]'],
                            'blue'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-500',   'border' => 'border-blue-100',   'badge' => 'bg-blue-100 text-blue-600'],
                            'green'  => ['bg' => 'bg-green-50',  'text' => 'text-green-500',  'border' => 'border-green-100',  'badge' => 'bg-green-100 text-green-700'],
                            'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-500', 'border' => 'border-purple-100', 'badge' => 'bg-purple-100 text-purple-700'],
                            'gray'   => ['bg' => 'bg-gray-50',   'text' => 'text-gray-600',   'border' => 'border-gray-200',   'badge' => 'bg-gray-100 text-gray-600'],
                            'red'    => ['bg' => 'bg-red-50',    'text' => 'text-red-500',    'border' => 'border-red-100',    'badge' => 'bg-red-100 text-red-600'],
                        ];
                        $c = $colorMap[$meta['color']] ?? $colorMap['gray'];
                    @endphp

                    <div class="type-section" data-type="{{ $type }}">
                        {{-- Titre de section --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-9 h-9 {{ $c['bg'] }} rounded-xl flex items-center justify-center {{ $c['text'] }}">
                                <i class="fas {{ $meta['icon'] }} text-sm"></i>
                            </div>
                            <h2 class="text-lg font-black text-[#1A1D1F]">{{ $meta['label'] }}</h2>
                            <span class="{{ $c['badge'] }} text-xs font-bold px-3 py-1 rounded-full">
                                {{ $items->count() }} demande{{ $items->count() > 1 ? 's' : '' }}
                            </span>
                        </div>

                        <div class="space-y-4">
                            @foreach($items as $declaration)
                                @php
                                    $statutCfg = match($declaration->statut) {
                                        'ouvert'   => ['label' => 'Ouvert',    'class' => 'bg-amber-100 text-amber-700',  'dot' => 'bg-amber-400'],
                                        'en_cours' => ['label' => 'En cours', 'class' => 'bg-blue-100 text-blue-700',    'dot' => 'bg-blue-500'],
                                        'ferme'    => ['label' => 'Traité',   'class' => 'bg-green-100 text-green-700',  'dot' => 'bg-green-500'],
                                        default    => ['label' => 'Inconnu',  'class' => 'bg-gray-100 text-gray-600',    'dot' => 'bg-gray-400'],
                                    };
                                @endphp

                                <div class="bg-white rounded-[24px] border {{ $c['border'] }} shadow-sm overflow-hidden hover:shadow-md transition-all">

                                    {{-- Card header --}}
                                    <div class="p-6 pb-4">
                                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                    <span class="inline-flex items-center gap-1.5 {{ $statutCfg['class'] }} text-xs font-bold px-3 py-1 rounded-full">
                                                        <span class="w-1.5 h-1.5 {{ $statutCfg['dot'] }} rounded-full"></span>
                                                        {{ $statutCfg['label'] }}
                                                    </span>
                                                    @if($declaration->statut === 'en_cours')
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex items-center gap-1.5 bg-[#e94f1b] text-white text-xs font-bold px-3 py-1 rounded-full animate-pulse shadow-sm shadow-[#e94f1b]/30">
                                                                <i class="fas fa-envelope text-[9px]"></i> Nouveau message
                                                            </span>
                                                            <form action="{{ route('user.support.mark-read', $declaration->id) }}" method="POST" class="inline">
                                                                @csrf
                                                                <button type="submit" class="w-6 h-6 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-400 hover:text-[#e94f1b] hover:border-[#e94f1b] transition-all shadow-sm" title="Marquer comme vu">
                                                                    <i class="fas fa-check text-[10px]"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @elseif($declaration->reponse)
                                                        <span class="inline-flex items-center gap-1.5 bg-[#e94f1b]/10 text-[#e94f1b] text-xs font-bold px-3 py-1 rounded-full">
                                                            <i class="fas fa-reply text-[9px]"></i> Répondu
                                                        </span>
                                                    @endif
                                                </div>
                                                <h3 class="text-base font-bold text-[#1A1D1F] truncate">{{ $declaration->objet }}</h3>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    Soumis le {{ $declaration->created_at->format('d/m/Y à H:i') }}
                                                </p>
                                            </div>
                                            <button onclick="toggleDetails('card-{{ $declaration->id }}')"
                                                    class="flex-shrink-0 w-9 h-9 bg-gray-50 hover:bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 transition-all">
                                                <i class="fas fa-chevron-down text-xs transition-transform" id="icon-{{ $declaration->id }}"></i>
                                            </button>
                                        </div>

                                        {{-- Réservation liée --}}
                                        @if($declaration->reservation)
                                            <div class="mt-3 inline-flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-xl px-3 py-2 text-xs text-gray-600">
                                                <i class="fas fa-ticket-alt text-gray-400"></i>
                                                Réservation #{{ $declaration->reservation->numero_reservation ?? $declaration->reservation->id }}
                                                @if($declaration->reservation->programme)
                                                    &mdash; {{ $declaration->reservation->programme->depart ?? '' }}
                                                    @if($declaration->reservation->date_voyage)
                                                        le {{ \Carbon\Carbon::parse($declaration->reservation->date_voyage)->format('d/m/Y') }}
                                                    @endif
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Détails dépliables --}}
                                    <div id="card-{{ $declaration->id }}" class="hidden px-6 pb-6">
                                        <div class="border-t border-gray-100 pt-4 space-y-4">

                                            {{-- Description --}}
                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5">Ma demande</p>
                                                <p class="text-sm text-gray-700 leading-relaxed bg-gray-50 rounded-2xl p-4">
                                                    {{ $declaration->description }}
                                                </p>
                                            </div>

                                            {{-- Chat / Échanges --}}
                                            <div class="space-y-4">
                                                @if($declaration->reponse && $declaration->messages->isEmpty())
                                                    <div>
                                                        <p class="text-xs font-bold text-[#e94f1b] uppercase tracking-widest mb-1.5">
                                                            <i class="fas fa-headset mr-1"></i> Réponse de l'administrateur
                                                        </p>
                                                        <div class="bg-gradient-to-br from-[#e94f1b]/5 to-[#e94f1b]/10 border border-[#e94f1b]/20 rounded-2xl p-4">
                                                            <p class="text-sm text-gray-800 leading-relaxed">{{ $declaration->reponse }}</p>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Affichage des messages --}}
                                                @foreach($declaration->messages as $msg)
                                                    @if($msg->sender_type == 'admin')
                                                        <div>
                                                            <p class="text-xs font-bold text-[#e94f1b] uppercase tracking-widest mb-1.5 flex justify-between">
                                                                <span><i class="fas fa-headset mr-1"></i> Administrateur</span>
                                                                <span class="text-gray-400 font-normal">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                                                            </p>
                                                            <div class="bg-gradient-to-br from-[#e94f1b]/5 to-[#e94f1b]/10 border border-[#e94f1b]/20 rounded-2xl p-4">
                                                                <p class="text-sm text-gray-800 leading-relaxed">{{ $msg->message }}</p>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div>
                                                            <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1.5 flex justify-between">
                                                                <span><i class="fas fa-user mr-1"></i> Moi</span>
                                                                <span class="text-gray-400 font-normal">{{ $msg->created_at->format('d/m/Y H:i') }}</span>
                                                            </p>
                                                            <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4">
                                                                <p class="text-sm text-gray-700 leading-relaxed">{{ $msg->message }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach

                                                @if($declaration->statut === 'ferme')
                                                    <div class="mt-3 inline-flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-2 text-xs text-green-700 font-bold w-full justify-center">
                                                        <i class="fas fa-check-circle text-green-500"></i>
                                                        Votre demande a été traitée et clôturée
                                                    </div>
                                                @else
                                                    @if(!$declaration->reponse && $declaration->messages->isEmpty())
                                                        <div class="flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-2xl px-4 py-3">
                                                            <div class="w-7 h-7 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                                                <i class="fas fa-hourglass-half text-amber-500 text-xs"></i>
                                                            </div>
                                                            <p class="text-xs text-amber-700 font-medium">
                                                                En attente de réponse — notre équipe reviendra vers vous prochainement.
                                                            </p>
                                                        </div>
                                                    @endif
                                                    
                                                    {{-- Formulaire pour répondre --}}
                                                    <div class="pt-4 border-t border-gray-100 mt-4">
                                                        <form action="{{ route('user.support.repondre', $declaration->id) }}" method="POST">
                                                            @csrf
                                                            <div class="flex flex-col gap-3">
                                                                <textarea name="reponse" rows="2" class="w-full bg-white border border-gray-200 rounded-xl p-3 text-sm focus:outline-none focus:border-[#e94f1b] focus:ring-1 focus:ring-[#e94f1b] transition-all resize-none" placeholder="Saisissez votre réponse..." required></textarea>
                                                                <div class="flex justify-end">
                                                                    <button type="submit" class="bg-[#e94f1b] text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-[#c73d12] transition-all shadow-sm flex items-center gap-2">
                                                                        <i class="fas fa-paper-plane text-xs"></i> Envoyer
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
    body { font-family: 'Outfit', sans-serif; }

    .tab-btn {
        background: white;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
    .tab-btn:hover {
        background: #f9fafb;
        color: #1A1D1F;
    }
    .active-tab {
        background: #1A1D1F !important;
        color: white !important;
        border-color: #1A1D1F !important;
    }
</style>

<script>
    function toggleDetails(id) {
        const el = document.getElementById(id);
        const cardId = id.replace('card-', '');
        const icon = document.getElementById('icon-' + cardId);

        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            el.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }

    function filterType(type) {
        // Update active tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active-tab');
        });
        document.querySelector(`[data-tab="${type}"]`).classList.add('active-tab');

        // Show/hide sections
        document.querySelectorAll('.type-section').forEach(section => {
            if (type === 'all' || section.dataset.type === type) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });
    }
</script>
@endsection
