@extends('chauffeur.layouts.template')

@section('styles')
<style>
    .detail-card {
        background: white;
        border-radius: 32px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .message-content {
        background: #f8fafc;
        border-radius: 24px;
        padding: 2rem;
        border: 1px solid #f1f5f9;
        font-size: 1.1rem;
        line-height: 1.8;
        color: #334155;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-6">
    <div class="mb-6">
        <a href="{{ route('chauffeur.messages.index') }}" class="inline-flex items-center text-slate-500 font-bold hover:no-underline">
            <i class="fas fa-chevron-left mr-2"></i>
            Retour aux messages
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="detail-card">
                <div class="p-8 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-black text-slate-900 mb-1">{{ $message->subject }}</h2>
                        <div class="flex items-center text-slate-400 text-sm font-bold">
                            <i class="far fa-clock mr-2"></i>
                            {{ $message->created_at->translatedFormat('d F Y à H:i') }}
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-50/50">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-orange-500 text-white rounded-xl flex items-center justify-center mr-4 shadow-lg shadow-orange-200">
                            <i class="fas fa-building text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-[10px] font-black text-orange-500 uppercase tracking-widest">Expéditeur :</span>
                            <h4 class="text-lg font-bold text-slate-900">{{ $message->compagnie->name ?? 'La Direction' }}</h4>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    <div class="message-content whitespace-pre-wrap">
                        {{ $message->message }}
                    </div>
                </div>

                <div class="p-8 border-t border-slate-50 bg-slate-50/20 flex justify-between items-center text-slate-400">
                    <span class="text-xs font-bold uppercase tracking-widest">Message ID: #{{ $message->id }}</span>
                    @if($message->is_read)
                    <div class="text-emerald-500 text-xs font-black uppercase tracking-widest flex items-center">
                        <i class="fas fa-check-double mr-2"></i> Message Lu
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
