@extends('agent.layouts.template')

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
        background: #fffafa;
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
    
    .msg-card.sent {
        border-left: 4px solid #3b82f6;
    }
    .msg-card.sent::before { display: none; }
    
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

    {{-- Flash messages --}}
    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-5 flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Header Section -->
    <div class="pro-header flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">Messagerie</h1>
            <p class="text-slate-500 font-medium text-sm">
                Connecté en tant que <span class="text-[#e94f1b] font-bold">{{ Auth::guard('agent')->user()->name }}</span>
            </p>
        </div>
        
        <div class="flex items-center gap-3">
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
            <button onclick="openComposeModal()" class="px-5 py-3 bg-[#e94f1b] hover:bg-[#d33d0f] text-white rounded-2xl font-bold text-sm transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-pen"></i>
                Écrire à la gare
            </button>
        </div>
    </div>

    {{-- Messages sent by agent --}}
    @php
        $sentMessages = \App\Models\GareMessage::where('sender_type', \App\Models\Agent::class)
            ->where('sender_id', Auth::guard('agent')->user()->id)
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    @if($sentMessages->count() > 0)
    <div class="mb-6">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
            <i class="fas fa-paper-plane text-blue-400"></i>
            Messages envoyés
        </h3>
        <div class="flex flex-col gap-2">
            @foreach($sentMessages as $sent)
            <div class="msg-card sent p-4 block">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </div>
                    <div class="flex-grow min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-slate-800 text-sm truncate">{{ $sent->subject }}</h4>
                            <span class="text-xs text-slate-400 whitespace-nowrap ml-2">{{ $sent->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-400 text-xs truncate mt-0.5">{{ $sent->message }}</p>
                    </div>
                    @if($sent->is_read)
                    <span class="text-green-400 text-xs"><i class="fas fa-check-double"></i></span>
                    @else
                    <span class="text-slate-300 text-xs"><i class="fas fa-check"></i></span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Received messages --}}
    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
        <i class="fas fa-inbox text-orange-400"></i>
        Boîte de réception
    </h3>
    <div class="flex flex-col gap-3">
        @forelse($messages as $message)
            <a href="{{ route('agent.messages.show', ['id' => $message->id, 'source' => $message->source]) }}" class="msg-card p-5 group hover:no-underline block {{ !$message->is_read ? 'unread' : '' }}">
                <div class="flex items-start gap-5">
                    <div class="sender-icon flex-shrink-0 group-hover:scale-110">
                        <i class="fas {{ $message->sender_icon ?? 'fa-building' }}"></i>
                    </div>
                    <div class="flex-grow min-w-0 pt-1">
                        <div class="flex items-center justify-between mb-1">
                            <h3 class="text-lg font-bold text-slate-800 truncate group-hover:text-[#e94f1b] transition-colors">
                                {{ $message->sender_name ?? 'La Direction' }}
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
                <p class="text-slate-400 max-w-sm mx-auto">Votre boîte de réception est vide. Les communications importantes apparaîtront ici.</p>
            </div>
        @endforelse
    </div>

    @if($messages->hasPages())
    <div class="mt-10 flex justify-center">
        {{ $messages->links() }}
    </div>
    @endif
</div>

{{-- Modal composer --}}
<div id="composeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-[#e94f1b] to-[#d33d0f] p-5 text-white">
            <div class="flex items-center justify-between">
                <h4 class="font-bold text-lg flex items-center gap-2">
                    <i class="fas fa-pen-to-square"></i>
                    Écrire à la gare
                </h4>
                <button onclick="closeComposeModal()" class="text-white/70 hover:text-white transition">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>
        <form action="{{ route('agent.messages.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Destinataire</label>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-600 flex items-center gap-2">
                        <i class="fas fa-warehouse text-[#e94f1b]"></i>
                        <span class="font-medium">
                            {{ Auth::guard('agent')->user()->gare->nom_gare ?? 'Ma Gare' }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sujet</label>
                    <input type="text" name="subject" required placeholder="Sujet du message..."
                           class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Message</label>
                    <textarea name="message" rows="5" required placeholder="Votre message..."
                              class="w-full border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e94f1b]/30 focus:border-transparent resize-none"></textarea>
                </div>
            </div>
            <div class="border-t border-slate-100 p-4 flex gap-3">
                <button type="button" onclick="closeComposeModal()" class="flex-1 py-3 border border-slate-200 rounded-xl text-slate-600 font-medium hover:bg-slate-50 transition text-sm">
                    Annuler
                </button>
                <button type="submit" class="flex-1 py-3 bg-[#e94f1b] hover:bg-[#d33d0f] text-white rounded-xl font-bold transition text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i>
                    Envoyer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openComposeModal() {
    document.getElementById('composeModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeComposeModal() {
    document.getElementById('composeModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.getElementById('composeModal').addEventListener('click', function(e) {
    if (e.target === this) closeComposeModal();
});
</script>
@endsection
