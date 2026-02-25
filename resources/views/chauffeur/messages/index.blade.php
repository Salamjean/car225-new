@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .message-card {
        border-radius: 16px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
    }
    .message-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #e2e8f0;
        transition: background 0.25s ease;
    }
    .message-card.unread::before {
        background: #e94f1b;
    }
    .message-card.unread {
        background: #fffafa;
        border-color: rgba(233, 79, 27, 0.1);
    }
    .message-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px -10px rgba(233, 79, 27, 0.12);
        border-color: rgba(233, 79, 27, 0.2);
    }
    .message-card.sent {
        border-left: 4px solid #3b82f6;
    }
    .message-card.sent::before { display: none; }
</style>
@endsection

@section('content')
<div class="container-fluid py-6 px-4">

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

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h2 class="text-2xl font-black text-gray-900">Messagerie</h2>
            <p class="text-gray-500 text-sm">Messages de votre compagnie et de votre gare</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="bg-orange-100 text-orange-600 px-4 py-2 rounded-xl font-bold text-sm">
                <i class="fas fa-envelope mr-1"></i>
                {{ $messages->total() }} message(s)
            </div>
            <button onclick="openComposeModal()" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-pen"></i>
                Écrire à la gare
            </button>
        </div>
    </div>

    {{-- Messages sent by chauffeur --}}
    @php
        $sentMessages = \App\Models\GareMessage::where('sender_type', \App\Models\Personnel::class)
            ->where('sender_id', Auth::guard('chauffeur')->user()->id)
            ->latest()
            ->limit(5)
            ->get();
    @endphp

    @if($sentMessages->count() > 0)
    <div class="mb-6">
        <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
            <i class="fas fa-paper-plane text-blue-400"></i>
            Messages envoyés
        </h3>
        <div class="grid gap-2">
            @foreach($sentMessages as $sent)
            <div class="message-card sent bg-white p-4 block">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0">
                        <i class="fas fa-paper-plane text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-gray-800 text-sm truncate">{{ $sent->subject }}</h4>
                            <span class="text-xs text-gray-400 whitespace-nowrap ml-2">{{ $sent->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-400 text-xs truncate mt-0.5">{{ $sent->message }}</p>
                    </div>
                    @if($sent->is_read)
                    <span class="text-green-400 text-xs"><i class="fas fa-check-double"></i></span>
                    @else
                    <span class="text-gray-300 text-xs"><i class="fas fa-check"></i></span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Received messages --}}
    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
        <i class="fas fa-inbox text-orange-400"></i>
        Boîte de réception
    </h3>
    <div class="grid gap-3">
        @forelse($messages as $message)
            <a href="{{ route('chauffeur.messages.show', ['id' => $message->id, 'source' => $message->source]) }}" class="message-card bg-white p-5 block hover:no-underline {{ !$message->is_read ? 'unread' : '' }}">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl {{ !$message->is_read ? 'bg-orange-50 text-orange-500' : 'bg-gray-50 text-gray-400' }} flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $message->sender_icon ?? 'fa-building' }} text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-base font-bold text-gray-900 truncate">{{ $message->sender_name ?? 'Direction' }}</h4>
                            <span class="text-xs text-gray-400 whitespace-nowrap bg-gray-50 px-2 py-0.5 rounded-lg ml-2">
                                {{ $message->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <h5 class="text-sm font-bold text-gray-600 mb-0.5 truncate">{{ $message->subject }}</h5>
                        <p class="text-gray-400 text-sm truncate">{{ $message->message }}</p>
                    </div>
                    @if(!$message->is_read)
                    <span class="px-2 py-0.5 bg-red-100 text-red-500 text-[10px] font-black uppercase rounded-full flex-shrink-0 mt-1">
                        Nouveau
                    </span>
                    @endif
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl p-16 text-center border border-gray-100">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-3xl text-gray-200"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Aucun message</h3>
                <p class="text-gray-400 mt-1 text-sm">Votre boîte de réception est vide.</p>
            </div>
        @endforelse
    </div>

    @if($messages->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $messages->links() }}
    </div>
    @endif
</div>

{{-- Modal composer --}}
<div id="composeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.5);">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-red-600 p-5 text-white">
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
        <form action="{{ route('chauffeur.messages.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Destinataire</label>
                    <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-600 flex items-center gap-2">
                        <i class="fas fa-warehouse text-orange-400"></i>
                        <span class="font-medium">
                            {{ Auth::guard('chauffeur')->user()->gare->nom_gare ?? 'Ma Gare' }}
                        </span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Sujet</label>
                    <input type="text" name="subject" required placeholder="Sujet du message..."
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Message</label>
                    <textarea name="message" rows="5" required placeholder="Votre message..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-orange-300 focus:border-transparent resize-none"></textarea>
                </div>
            </div>
            <div class="border-t border-gray-100 p-4 flex gap-3">
                <button type="button" onclick="closeComposeModal()" class="flex-1 py-3 border border-gray-200 rounded-xl text-gray-600 font-medium hover:bg-gray-50 transition text-sm">
                    Annuler
                </button>
                <button type="submit" class="flex-1 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold transition text-sm flex items-center justify-center gap-2">
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
