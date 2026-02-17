@extends('compagnie.layouts.template')

@section('styles')
<style>
    :root {
        --primary-accent: #e94f1b; /* Your base red/orange */
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

    .avatar-glow {
        position: relative;
    }

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

    /* Floating pagination */
    .pagination-wrapper .pagination {
        gap: 8px;
    }
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
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Action Header -->
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

    <!-- Main Workspace -->
    <div class="glass-card bg-white min-h-[600px] flex flex-col">
        <!-- Dashboard Filters -->
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

        <!-- Inbox Stream -->
        <div class="flex-grow py-4">
            @if(session('success'))
                <div class="mx-6 mb-6 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-xl flex items-center shadow-sm">
                    <div class="bg-emerald-500 text-white p-2 rounded-lg mr-3 shadow-md shadow-emerald-200">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="text-emerald-800 font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="space-y-1">
                @forelse($messages as $message)
                    @php
                        $recipient = $message->recipient;
                        $initials = $recipient ? strtoupper(substr($recipient->name, 0, 1) . substr($recipient->prenom, 0, 1)) : '??';
                        
                        $typeConfig = [
                            'App\\Models\\Agent' => ['icon' => 'fa-user-tie', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100'],
                            'App\\Models\\Caisse' => ['icon' => 'fa-cash-register', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-100'],
                            'App\\Models\\Personnel' => ['icon' => 'fa-steering-wheel', 'color' => 'text-purple-600', 'bg' => 'bg-purple-100'],
                        ][$message->recipient_type] ?? ['icon' => 'fa-user', 'color' => 'text-slate-400', 'bg' => 'bg-slate-100'];
                    @endphp

                    <a href="{{ route('compagnie.messages.show', $message->id) }}" class="list-group-item list-group-item-action message-item p-4 bg-transparent border-0 hover:no-underline">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                            <!-- Recipient Avatar -->
                            <div class="flex items-center flex-shrink-0">
                                <div class="avatar-glow text-orange-600">
                                    <div class="w-14 h-14 rounded-2xl bg-orange-100 flex items-center justify-center font-bold text-lg shadow-inner">
                                        {{ $initials }}
                                    </div>
                                </div>
                                <div class="ml-4 lg:hidden block">
                                    <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $recipient->name ?? 'Indéfini' }} {{ $recipient->prenom ?? '' }}</h4>
                                    <span class="text-sm font-semibold {{ $typeConfig['color'] }} uppercase tracking-wider">{{ str_replace('App\\Models\\', '', $message->recipient_type) }}</span>
                                </div>
                            </div>

                            <!-- Content Preview -->
                            <div class="flex-grow lg:px-4">
                                <div class="hidden lg:flex items-center mb-1 gap-2">
                                    <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $recipient->name ?? 'Indéfini' }} {{ $recipient->prenom ?? '' }}</h4>
                                    <span class="mx-1 text-slate-300">&middot;</span>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded-md {{ $typeConfig['bg'] }} {{ $typeConfig['color'] }} uppercase">
                                        {{ str_replace('App\\Models\\', '', $message->recipient_type) }}
                                    </span>
                                </div>
                                <h5 class="text-md font-bold text-slate-700 mb-1 truncate">{{ $message->subject }}</h5>
                                <p class="text-slate-500 text-sm line-clamp-1 italic">{{ $message->message }}</p>
                            </div>

                            <!-- Meta & Badges -->
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
                            
                            <!-- Action indicator (desktop) -->
                            <div class="hidden lg:flex items-center justify-center w-10 h-10 rounded-full bg-slate-50 text-slate-300 transition-colors group-hover:bg-orange-50 group-hover:text-orange-400 p-0">
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
                        <p class="text-slate-500 max-w-sm mt-2">Vous n'avez envoyé aucun message dans cette catégorie pour le moment.</p>
                        <a href="{{ route('compagnie.messages.create') }}" class="mt-8 btn btn-outline-primary rounded-xl px-4 py-2 hover:bg-orange-500 border-slate-200 text-slate-600 font-bold">
                            Envoyer premier message
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Footer / Pagination -->
        @if($messages->hasPages())
        <div class="border-t border-slate-100 p-6 flex justify-center pagination-wrapper">
            {{ $messages->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
