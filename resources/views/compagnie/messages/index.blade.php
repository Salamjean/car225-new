@extends('compagnie.layouts.template')

@section('styles')
<style>
    :root {
        --primary-accent: #e94f1b;
        --primary-accent-glow: rgba(233, 79, 27, 0.15);
        --glass-bg: rgba(255, 255, 255, 0.95);
        --text-main: #1a202c;
        --text-muted: #718096;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    .nav-pill-custom .nav-link {
        color: var(--text-muted);
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        padding: 0.6rem 1.5rem;
        border-radius: 14px;
    }

    .nav-pill-custom .nav-link.active {
        background: white !important;
        color: var(--primary-accent) !important;
        border-color: var(--primary-accent-glow);
        box-shadow: 0 4px 12px var(--primary-accent-glow);
    }

    .message-item {
        border-radius: 18px;
        margin: 0.5rem 1rem;
        border: 1px solid transparent !important;
        transition: all 0.25s ease;
    }

    .message-item:hover {
        background: #fff !important;
        border-color: #edf2f7 !important;
        transform: translateX(8px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .avatar-glow { position: relative; }
    .avatar-glow::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.15;
        z-index: -1;
    }

    .btn-glow-primary {
        background: var(--primary-accent);
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 8px 16px var(--primary-accent-glow);
    }
    .btn-glow-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px var(--primary-accent-glow);
        color: white;
    }

    .pagination-wrapper .pagination { gap: 8px; }
    .pagination-wrapper .page-link {
        border-radius: 10px !important;
        border: none;
        color: var(--text-muted);
        background: #f7fafc;
    }
    .pagination-wrapper .page-item.active .page-link {
        background: var(--primary-accent);
        color: white;
    }

    .main-tab-btn {
        padding: 0.75rem 1.5rem;
        font-weight: 700;
        border-radius: 16px 16px 0 0;
        border: 1px solid transparent;
        border-bottom: none;
        background: transparent;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .main-tab-btn.active {
        background: white;
        color: var(--primary-accent);
        border-color: #edf2f7;
    }
    .main-tab-btn .badge-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        margin-left: 0.5rem;
        padding: 0 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Communication</h2>
            <p class="text-slate-500 mt-1 font-medium d-flex align-items-center">
                <span class="w-2 h-2 rounded-full bg-orange-500 mr-2"></span>
                Espace d'échange professionnel Car225
            </p>
        </div>
        <a href="{{ route('compagnie.messages.create') }}" class="btn btn-glow-primary rounded-2xl px-6 py-3 font-bold d-inline-flex align-items-center">
            <i class="fas fa-plus-circle text-xl mr-2"></i>
            Nouveau Message
        </a>
    </div>

    <!-- Main Tabs -->
    <div class="flex gap-2 mb-0">
        <button class="main-tab-btn active" onclick="switchMainTab('received', this)" id="tab-received">
            <i class="fas fa-inbox mr-2"></i> Messages Reçus des Gares
            <span class="badge-count {{ $unreadReceivedCount > 0 ? 'bg-red-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                {{ $unreadReceivedCount > 0 ? $unreadReceivedCount : $receivedMessages->total() }}
            </span>
        </button>
        <button class="main-tab-btn" onclick="switchMainTab('sent', this)" id="tab-sent">
            <i class="fas fa-paper-plane mr-2"></i> Messages Envoyés
            <span class="badge-count bg-slate-100 text-slate-600">{{ $messages->total() }}</span>
        </button>
    </div>

    <!-- RECEIVED MESSAGES FROM GARES (DEFAULT) -->
    <div id="panel-received">
        <div class="glass-card bg-white min-h-[500px] flex flex-col" style="border-top-left-radius: 0;">
            <div class="p-4 border-b border-slate-100 bg-slate-50/30">
                <div class="flex items-center gap-3 px-2">
                    <i class="fas fa-warehouse text-orange-500 text-xl"></i>
                    <span class="font-bold text-slate-700">Messages reçus de vos gares</span>
                </div>
            </div>

            <div class="flex-grow py-4">
                <div class="space-y-1">
                    @forelse($receivedMessages as $gareMsg)
                        <a href="{{ route('compagnie.messages.show-received', $gareMsg->id) }}" class="list-group-item list-group-item-action message-item p-4 bg-transparent border-0 hover:no-underline">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                <div class="flex items-center flex-shrink-0">
                                    <div class="avatar-glow text-orange-600">
                                        <div class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center font-bold text-lg shadow-inner">
                                            <i class="fas fa-warehouse"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4 lg:hidden block">
                                        <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $gareMsg->gare->nom_gare ?? 'Gare' }}</h4>
                                        <span class="text-sm font-semibold text-orange-600 uppercase tracking-wider">Gare</span>
                                    </div>
                                </div>

                                <div class="flex-grow lg:px-4">
                                    <div class="hidden lg:flex items-center mb-1 gap-2">
                                        <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $gareMsg->gare->nom_gare ?? 'Gare' }}</h4>
                                        <span class="mx-1 text-slate-300">&middot;</span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-orange-100 text-orange-600 uppercase">Gare</span>
                                    </div>
                                    <h5 class="text-md font-bold text-slate-700 mb-1 {{ !$gareMsg->is_read ? 'text-slate-900' : '' }} truncate">{{ $gareMsg->subject }}</h5>
                                    <p class="text-slate-500 text-sm line-clamp-1 italic">{{ $gareMsg->message }}</p>
                                </div>

                                <div class="flex items-center lg:flex-col lg:items-end justify-between lg:justify-center gap-2 flex-shrink-0 min-w-[120px]">
                                    <span class="text-xs font-bold text-slate-400 lg:order-1">
                                        {{ $gareMsg->created_at->translatedFormat('d M, H:i') }}
                                    </span>
                                    <div class="lg:order-2">
                                        @if($gareMsg->is_read)
                                            <div class="flex items-center text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full text-xs font-extrabold border border-emerald-100">
                                                <i class="fas fa-check-double mr-1.5"></i> LU
                                            </div>
                                        @else
                                            <div class="flex items-center text-orange-500 bg-orange-50 px-3 py-1 rounded-full text-xs font-extrabold border border-orange-100 animate-pulse">
                                                <i class="fas fa-envelope mr-1.5"></i> NOUVEAU
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="hidden lg:flex items-center justify-center w-10 h-10 rounded-full bg-slate-50 text-slate-300 p-0">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                            <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-warehouse text-5xl text-slate-200"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800">Aucun message reçu</h3>
                            <p class="text-slate-500 max-w-sm mt-2">Aucun message de vos gares pour le moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($receivedMessages->hasPages())
            <div class="border-t border-slate-100 p-6 flex justify-center pagination-wrapper">
                {{ $receivedMessages->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- SENT MESSAGES PANEL -->
    <div id="panel-sent" style="display: none;">
        <div class="glass-card bg-white min-h-[500px] flex flex-col" style="border-top-right-radius: 0;">
            <div class="p-4 border-b border-slate-100 bg-slate-50/30">
                <ul class="nav nav-pill-custom gap-2 overflow-x-auto print:hidden">
                    <li class="nav-item">
                        <a class="nav-link {{ !request('type') ? 'active' : '' }}" href="{{ route('compagnie.messages.index') }}">
                            <i class="fas fa-layer-group opacity-70 mr-2"></i> Flux global
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'agent' ? 'active' : '' }}" href="{{ route('compagnie.messages.index', ['type' => 'agent']) }}">
                            <i class="fas fa-user-tie opacity-70 mr-2"></i> Agents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'personnel' ? 'active' : '' }}" href="{{ route('compagnie.messages.index', ['type' => 'personnel']) }}">
                            <i class="fas fa-steering-wheel opacity-70 mr-2"></i> Chauffeurs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('type') == 'caisse' ? 'active' : '' }}" href="{{ route('compagnie.messages.index', ['type' => 'caisse']) }}">
                            <i class="fas fa-cash-register opacity-70 mr-2"></i> Points Caisse
                        </a>
                    </li>
                </ul>
            </div>

            <div class="flex-grow py-4">
                <div class="space-y-1">
                    @forelse($messages as $message)
                        @php
                            $recipient = $message->recipient;
                            $recipientName = 'Indéfini';
                            $initials = '??';
                            
                            if ($recipient) {
                                if ($message->recipient_type === 'App\Models\Gare') {
                                    $recipientName = $recipient->nom_gare;
                                    $initials = strtoupper(substr($recipient->nom_gare, 0, 2));
                                } else {
                                    $recipientName = ($recipient->name ?? '') . ' ' . ($recipient->prenom ?? '');
                                    $initials = strtoupper(substr($recipient->name ?? '', 0, 1) . substr($recipient->prenom ?? '', 0, 1));
                                }
                            }

                            $typeConfig = [
                                'App\\Models\\Agent' => ['icon' => 'fa-user-tie', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100', 'label' => 'Agent'],
                                'App\\Models\\Caisse' => ['icon' => 'fa-cash-register', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-100', 'label' => 'Caisse'],
                                'App\\Models\\Personnel' => ['icon' => 'fa-steering-wheel', 'color' => 'text-purple-600', 'bg' => 'bg-purple-100', 'label' => 'Chauffeur'],
                                'App\\Models\\Gare' => ['icon' => 'fa-warehouse', 'color' => 'text-orange-600', 'bg' => 'bg-orange-100', 'label' => 'Gare'],
                                'App\\Models\\User' => ['icon' => 'fa-user-shield', 'color' => 'text-indigo-600', 'bg' => 'bg-indigo-100', 'label' => 'Admin/User'],
                            ][$message->recipient_type] ?? ['icon' => 'fa-user', 'color' => 'text-slate-400', 'bg' => 'bg-slate-100', 'label' => 'Inconnu'];
                        @endphp

                        <a href="{{ route('compagnie.messages.show', $message->id) }}" class="list-group-item list-group-item-action message-item p-4 bg-transparent border-0 hover:no-underline">
                            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                                <div class="flex items-center flex-shrink-0">
                                    <div class="avatar-glow text-orange-600">
                                        <div class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center font-bold text-lg shadow-inner">
                                            {{ $initials }}
                                        </div>
                                    </div>
                                    <div class="ml-4 lg:hidden block">
                                        <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $recipientName }}</h4>
                                        <span class="text-sm font-semibold {{ $typeConfig['color'] }} uppercase tracking-wider">{{ $typeConfig['label'] }}</span>
                                    </div>
                                </div>

                                <div class="flex-grow lg:px-4">
                                    <div class="hidden lg:flex items-center mb-1 gap-2">
                                        <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $recipientName }}</h4>
                                        <span class="mx-1 text-slate-300">&middot;</span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $typeConfig['bg'] }} {{ $typeConfig['color'] }} uppercase">
                                            {{ $typeConfig['label'] }}
                                        </span>
                                    </div>
                                    <h5 class="text-md font-bold text-slate-700 mb-1 truncate">{{ $message->subject }}</h5>
                                    <p class="text-slate-500 text-sm line-clamp-1 italic">{{ $message->message }}</p>
                                </div>

                                <div class="flex items-center lg:flex-col lg:items-end justify-between lg:justify-center gap-2 flex-shrink-0 min-w-[120px]">
                                    <span class="text-xs font-bold text-slate-400 lg:order-1">
                                        {{ $message->created_at->translatedFormat('d M, H:i') }}
                                    </span>
                                    <div class="lg:order-2">
                                        @if($message->is_read)
                                            <div class="flex items-center text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full text-xs font-extrabold border border-emerald-100">
                                                <i class="fas fa-check-double mr-1.5"></i> LU
                                            </div>
                                        @else
                                            <div class="flex items-center text-slate-400 bg-slate-50 px-3 py-1 rounded-full text-xs font-extrabold border border-slate-100">
                                                <i class="fas fa-paper-plane mr-1.5"></i> ENVOYÉ
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="hidden lg:flex items-center justify-center w-10 h-10 rounded-full bg-slate-50 text-slate-300 p-0">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center py-20 px-4 text-center">
                            <div class="w-32 h-32 bg-slate-50 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-envelope-open text-5xl text-slate-200"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800">Aucune communication</h3>
                            <p class="text-slate-500 max-w-sm mt-2">Vous n'avez envoyé aucun message dans cette catégorie.</p>
                            <a href="{{ route('compagnie.messages.create') }}" class="mt-8 btn btn-outline-primary rounded-xl px-4 py-2 hover:bg-orange-500 border-slate-200 text-slate-600 font-bold">
                                Envoyer premier message
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($messages->hasPages())
            <div class="border-t border-slate-100 p-6 flex justify-center pagination-wrapper">
                {{ $messages->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function switchMainTab(tab, btn) {
    document.getElementById('panel-sent').style.display = tab === 'sent' ? 'block' : 'none';
    document.getElementById('panel-received').style.display = tab === 'received' ? 'block' : 'none';
    document.querySelectorAll('.main-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
}
</script>
@endsection
