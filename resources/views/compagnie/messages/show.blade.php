@extends('compagnie.layouts.template')

@section('styles')
<style>
    :root {
        --primary-accent: #e94f1b;
        --primary-accent-glow: rgba(233, 79, 27, 0.1);
        --glass-bg: rgba(255, 255, 255, 0.98);
    }

    .glass-detail-card {
        background: var(--glass-bg);
        border: 1px solid #edf2f7;
        border-radius: 32px;
        box-shadow: 0 20px 40px -15px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .message-bubble {
        background: #f8fafc;
        border-radius: 24px;
        padding: 2.5rem;
        position: relative;
        border: 1px solid #f1f5f9;
    }

    .recipient-chip {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
    }

    .status-badge {
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 0.5rem 1.25rem;
        border-radius: 12px;
        font-size: 0.75rem;
    }

    .back-btn {
        transition: all 0.3s ease;
        border-radius: 12px;
    }

    .back-btn:hover {
        background: #f1f5f9;
        transform: translateX(-5px);
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Navigation -->
    <div class="mb-6">
        <a href="{{ route('compagnie.messages.index') }}" class="back-btn inline-flex items-center px-4 py-2 text-slate-500 font-bold hover:no-underline">
            <i class="fas fa-chevron-left mr-3"></i>
            Retour aux messages
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="glass-detail-card">
                <!-- Message Top Bar -->
                <div class="px-8 py-6 border-b border-slate-100 bg-white flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-orange-500 text-white flex items-center justify-center shadow-lg shadow-orange-200">
                            <i class="fas fa-envelope-open-text text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-black text-slate-900 leading-tight mb-1">{{ $message->subject }}</h1>
                            <div class="text-slate-400 text-sm font-medium flex items-center">
                                <i class="far fa-clock mr-2"></i>
                                Envoyé le {{ $message->created_at->translatedFormat('d F Y') }} à {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if($message->is_read)
                            <span class="status-badge bg-emerald-50 text-emerald-600 border border-emerald-100 inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                                MESSAGE CONSULTÉ
                            </span>
                        @else
                            <span class="status-badge bg-slate-50 text-slate-500 border border-slate-100 inline-flex items-center">
                                <span class="w-2 h-2 rounded-full bg-slate-300 mr-2"></span>
                                EN ATTENTE DE LECTURE
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Recipient Info Panel -->
                <div class="px-8 py-8 bg-slate-50/50">
                    <div class="recipient-chip shadow-sm flex items-center max-w-fit">
                        @php
                            $recipient = $message->recipient;
                            $initials = $recipient ? strtoupper(substr($recipient->name, 0, 1) . substr($recipient->prenom, 0, 1)) : '??';
                            
                            $typeMeta = [
                                'App\\Models\\Agent' => ['label' => 'Agent Commercial', 'theme' => 'text-blue-600', 'bg' => 'bg-blue-600'],
                                'App\\Models\\Caisse' => ['label' => 'Point de Caisse', 'theme' => 'text-emerald-600', 'bg' => 'bg-emerald-600'],
                                'App\\Models\\Personnel' => ['label' => 'Chauffeur / Personnel', 'theme' => 'text-purple-600', 'bg' => 'bg-purple-600'],
                            ][$message->recipient_type] ?? ['label' => 'Utilisateur', 'theme' => 'text-slate-600', 'bg' => 'bg-slate-600'];
                        @endphp

                        <div class="w-12 h-12 rounded-xl {{ $typeMeta['bg'] }} text-white flex items-center justify-center font-black text-lg mr-4">
                            {{ $initials }}
                        </div>
                        
                        <div>
                            <span class="block text-[10px] font-black uppercase tracking-widest leading-none mb-1 {{ $typeMeta['theme'] }}">DESTINATAIRE :</span>
                            <h4 class="text-lg font-bold text-slate-900 leading-none">
                                {{ $recipient->name ?? 'Indéfini' }} {{ $recipient->prenom ?? '' }}
                            </h4>
                            <span class="text-xs font-semibold text-slate-400 mt-1 block">{{ $typeMeta['label'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Actual Message Body -->
                <div class="px-8 py-10 bg-white">
                    <div class="message-bubble min-h-[300px]">
                        <!-- Decorative quote mark -->
                        <div class="absolute top-6 left-6 opacity-[0.03] text-8xl font-serif pointer-events-none">"</div>
                        
                        <div class="relative z-10 text-slate-700 leading-loose text-lg font-medium whitespace-pre-wrap">
                            {{ $message->message }}
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/20 flex justify-between items-center">
                    <p class="text-xs font-bold text-slate-400 italic">Identifiant unique du message: #MSG-{{ $message->id }}</p>
                    
                    <div class="flex gap-3">
                        <button onclick="window.print()" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold flex items-center hover:bg-slate-50">
                            <i class="fas fa-print mr-2"></i> Imprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
