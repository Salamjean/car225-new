@extends('caisse.layouts.template')

@section('styles')
<style>
    .message-container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .back-btn {
        transition: all 0.2s ease;
    }
    .back-btn:hover {
        transform: translateX(-3px);
        color: #e94f1b;
    }
    
    .detail-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        border: 1px solid rgba(241, 245, 249, 1);
    }
    
    .meta-section {
        background: radial-gradient(circle at top right, #fff7ed 0%, #fff 40%);
        border-bottom: 1px solid #f1f5f9;
    }
    
    .sender-avatar {
        width: 64px;
        height: 64px;
        background: white;
        border: 1px solid #fed7aa;
        color: #e94f1b;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        box-shadow: 0 10px 15px -3px rgba(234, 88, 12, 0.1);
    }
    
    .message-body {
        font-size: 1.125rem;
        line-height: 1.8;
        color: #334155;
        white-space: pre-wrap;
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-badge.read {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #dcfce7;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-8">
    <div class="message-container">
        <!-- Navigation -->
        <div class="mb-8">
            <a href="{{ route('caisse.messages.index') }}" class="back-btn inline-flex items-center text-slate-500 font-bold hover:no-underline gap-2">
                <i class="fas fa-arrow-left"></i>
                <span>Retour à la liste</span>
            </a>
        </div>

        <div class="detail-card">
            <!-- Header / Meta -->
            <div class="meta-section p-8 md:p-10">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                    <div class="flex items-start gap-6">
                        <div class="sender-avatar flex-shrink-0">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="px-2.5 py-0.5 rounded-md bg-orange-100 text-[#e94f1b] text-[10px] font-black uppercase tracking-widest">
                                    Direction
                                </span>
                                <span class="text-slate-400 text-xs font-bold">
                                    {{ $message->created_at->isoFormat('LLLL') }}
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-black text-slate-900 leading-tight mb-2">
                                {{ $message->subject }}
                            </h1>
                            <p class="text-slate-500 font-medium text-sm">
                                De: <span class="text-slate-900">{{ $message->compagnie->name ?? 'La Direction' }}</span>
                            </p>
                        </div>
                    </div>
                    
                    <div>
                        @if($message->is_read)
                        <div class="status-badge read shadow-sm">
                            <i class="fas fa-check-double"></i>
                            <span>Lu le {{ $message->updated_at->format('d/m/Y') }}</span>
                        </div>
                        @else
                        <span class="px-3 py-1 bg-[#fff1f2] text-[#e94f1b] text-xs font-black uppercase tracking-widest rounded-full ring-1 ring-[#e94f1b]/20">
                            Nouveau
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8 md:p-12 bg-white relative">
                <!-- Decorative quote -->
                <i class="fas fa-quote-left absolute top-8 left-8 text-slate-100 text-6xl -z-0"></i>
                
                <div class="message-body relative z-10 pl-6 border-l-4 border-slate-100 ml-2">
                    {{ $message->message }}
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-slate-50 px-8 py-4 border-t border-slate-100 flex justify-between items-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID: #{{ str_pad($message->id, 5, '0', STR_PAD_LEFT) }}</span>
                <button class="text-slate-400 hover:text-[#e94f1b] transition-colors" title="Imprimer">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
