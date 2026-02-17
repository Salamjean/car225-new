@extends('caisse.layouts.template')

@section('styles')
<style>
    .pro-header {
        position: relative;
        padding-bottom: 2rem;
        margin-bottom: 2rem;
    }
    .pro-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100px;
        height: 4px;
        background: linear-gradient(90deg, #e94f1b, transparent);
        border-radius: 2px;
    }
    
    .msg-card {
        background: white;
        border-radius: 16px;
        border: 1px solid rgba(241, 245, 249, 1);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .msg-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #e2e8f0;
        transition: background 0.25s ease;
    }
    
    .msg-card.unread::before {
        background: #e94f1b;
    }
    
    .msg-card.unread {
        background: #fffafa; /* Very subtle orange tint */
        border-color: rgba(233, 79, 27, 0.1);
    }
    
    .msg-card:hover {
        transform: translateY(-3px) scale(1.005);
        box-shadow: 0 12px 24px -10px rgba(233, 79, 27, 0.15);
        border-color: rgba(233, 79, 27, 0.3);
        z-index: 10;
    }

    .msg-card:hover::before {
        width: 6px;
    }
    
    .sender-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: all 0.3s ease;
    }
    
    .msg-card.unread .sender-icon {
        background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
        color: #e94f1b;
    }
    
    .empty-state-icon {
        width: 120px;
        height: 120px;
        background: #f8fafc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        position: relative;
    }
    
    .empty-state-icon::after {
        content: '';
        position: absolute;
        inset: -10px;
        border-radius: 50%;
        border: 2px dashed #e2e8f0;
        animation: spin 10s linear infinite;
    }
    
    @keyframes spin { 100% { transform: rotate(360deg); } }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-8">
    <!-- Header Section -->
    <div class="pro-header flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Messagerie</h1>
            <p class="text-slate-500 font-medium text-sm">
                Connecté en tant que <span class="text-[#e94f1b] font-bold">{{ Auth::guard('caisse')->user()->name }}</span>
            </p>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="px-5 py-3 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
                <div class="relative">
                    <i class="fas fa-inbox text-slate-400 text-lg"></i>
                    @if($messages->where('is_read', false)->count() > 0)
                    <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-[#e94f1b] rounded-full ring-2 ring-white"></span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total</span>
                    <span class="text-lg font-black text-slate-800 leading-none">{{ $messages->total() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Grid -->
    <div class="flex flex-col gap-3">
        @forelse($messages as $message)
            <a href="{{ route('caisse.messages.show', $message->id) }}" class="msg-card p-5 group hover:no-underline block">
                <div class="flex items-start gap-5">
                    <!-- Icon -->
                    <div class="sender-icon flex-shrink-0 group-hover:scale-110">
                        <i class="fas fa-building"></i>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-grow min-w-0 pt-1">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-lg font-bold text-slate-800 truncate group-hover:text-[#e94f1b] transition-colors">
                                {{ $message->compagnie->name ?? 'La Direction' }}
                            </h3>
                            <span class="text-xs font-bold text-slate-400 whitespace-nowrap bg-slate-50 px-2 py-1 rounded-lg">
                                {{ $message->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <h4 class="text-sm font-bold text-slate-600 mb-1 truncate">{{ $message->subject }}</h4>
                        <p class="text-slate-400 text-sm line-clamp-1 group-hover:text-slate-500 transition-colors">
                            {{ $message->message }}
                        </p>
                    </div>
                    
                    <!-- New Badge -->
                    @if(!$message->is_read)
                    <div class="flex-shrink-0 self-center">
                        <span class="px-3 py-1 bg-[#fff1f2] text-[#e94f1b] text-[10px] font-black uppercase tracking-widest rounded-full ring-1 ring-[#e94f1b]/20">
                            Nouveau
                        </span>
                    </div>
                    @else
                    <div class="flex-shrink-0 self-center opacity-0 group-hover:opacity-100 transition-opacity transform translate-x-2 group-hover:translate-x-0">
                        <i class="fas fa-chevron-right text-slate-300"></i>
                    </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="bg-white rounded-3xl p-16 text-center border border-slate-100 shadow-sm">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox text-4xl text-slate-300"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-2">Tout est calme</h3>
                <p class="text-slate-400 max-w-sm mx-auto">Votre boîte de réception est vide. Les communications importantes de la direction apparaîtront ici.</p>
            </div>
        @endforelse
    </div>

    @if($messages->hasPages())
    <div class="mt-10 flex justify-center">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection
