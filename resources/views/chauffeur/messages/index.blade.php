@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .message-card {
        border-radius: 20px;
        transition: all 0.3s ease;
        border: 1px solid #eee;
    }
    .message-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
        border-color: #e94f1b;
    }
    .status-dot {
        height: 10px;
        width: 10px;
        border-radius: 50%;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-black text-slate-800">Boîte de réception</h2>
            <p class="text-slate-500 font-medium">Messages importants de votre compagnie</p>
        </div>
        <div class="bg-orange-100 text-orange-600 px-4 py-2 rounded-xl font-bold">
            <i class="fas fa-envelope mr-2"></i>
            {{ $messages->total() }} message(s)
        </div>
    </div>

    <div class="grid gap-4">
        @forelse($messages as $message)
            <a href="{{ route('chauffeur.messages.show', $message->id) }}" class="message-card bg-white p-5 shadow-sm block hover:no-underline">
                <div class="flex items-start justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mr-4">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-slate-900">{{ $message->compagnie->name ?? 'Direction' }}</h4>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                                {{ $message->created_at->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                    </div>
                    @if(!$message->is_read)
                        <span class="bg-red-500 status-dot shadow-lg shadow-red-200 animate-pulse"></span>
                    @endif
                </div>
                
                <div class="mt-4">
                    <h5 class="text-md font-bold text-slate-700 mb-1">{{ $message->subject }}</h5>
                    <p class="text-slate-500 text-sm line-clamp-1 italic">{{ $message->message }}</p>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-3xl p-20 text-center shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-3xl text-slate-200"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800">Aucun message</h3>
                <p class="text-slate-500 mt-2">Votre boîte de réception est vide pour le moment.</p>
            </div>
        @endforelse
    </div>

    @if($messages->hasPages())
    <div class="mt-8 flex justify-center">
        {{ $messages->links() }}
    </div>
    @endif
</div>
@endsection
