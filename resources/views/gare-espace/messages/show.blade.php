@extends('gare-espace.layouts.template')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="msg-show-wrapper">
    <div class="mb-4">
        <a href="{{ route('gare-espace.messages.index') }}" class="back-link">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la boîte de réception
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="msg-detail-card animate__animated animate__fadeInUp">
                <!-- Message Header -->
                <div class="msg-detail-header">
                    <div class="msg-detail-meta">
                        <div class="msg-detail-avatar">
                            <i class="fas {{ $message->sender_icon }}"></i>
                        </div>
                        <div>
                            @if($message->source === 'received')
                                <p class="msg-detail-to">De : <strong>{{ $message->sender_name }}</strong></p>
                            @else
                                <p class="msg-detail-to">
                                    À :
                                    <strong>
                                        @if($message->recipient)
                                            {{ $message->recipient->name ?? $message->recipient->nom_gare }} {{ $message->recipient->prenom ?? '' }}
                                        @else
                                            Destinataire inconnu
                                        @endif
                                    </strong>
                                </p>
                            @endif
                            <div class="msg-detail-info">
                                <span class="msg-badge">{{ $message->sender_type_label }}</span>
                                <span class="msg-date-detail">{{ $message->created_at->format('d/m/Y à H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subject -->
                <div class="msg-detail-subject">
                    <h1 class="text-2xl font-black text-slate-800">{{ $message->subject }}</h1>
                </div>

                <!-- Body -->
                <div class="msg-detail-body text-slate-600">
                    {!! nl2br(e($message->message)) !!}
                </div>

                <!-- Footer -->
                <div class="msg-detail-footer">
                    <a href="{{ route('gare-espace.messages.create') }}" class="btn-reply">
                        <i class="fas fa-reply"></i> Nouveau message
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary: #e94f1b;
    --primary-dark: #c13e13;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --font-family: 'Plus Jakarta Sans', sans-serif;
}

.msg-show-wrapper {
    padding: 2rem;
    font-family: var(--font-family);
    max-width: 1200px;
    margin: 0 auto;
}

.back-link {
    color: #94a3b8;
    font-weight: 700;
    text-decoration: none !important;
    transition: color 0.2s;
    display: inline-flex;
    align-items: center;
}

.back-link:hover {
    color: var(--primary);
}

.msg-detail-card {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 1.5rem;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
    overflow: hidden;
}

.msg-detail-header {
    padding: 1.75rem 2rem;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}

.msg-detail-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.msg-detail-avatar {
    width: 52px;
    height: 52px;
    border-radius: 1rem;
    background: linear-gradient(135deg, var(--primary), #ff6b3d);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.msg-detail-to {
    color: var(--text-main);
    font-size: 0.95rem;
    margin: 0 0 0.25rem;
}

.msg-detail-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.msg-badge {
    background: #f1f5f9;
    color: var(--text-muted);
    padding: 0.2rem 0.7rem;
    border-radius: 1rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
}

.msg-date-detail {
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 500;
}

.msg-detail-subject {
    padding: 1.5rem 2rem 0;
}

.msg-detail-subject h2 {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
}

.msg-detail-body {
    padding: 1.5rem 2rem 2rem;
    color: var(--text-main);
    font-size: 0.95rem;
    line-height: 1.8;
}

.msg-detail-footer {
    padding: 1.25rem 2rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: flex-end;
}

.btn-reply {
    background: var(--primary);
    color: white !important;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none !important;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-reply:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

@media (max-width: 768px) {
    .msg-show-wrapper { padding: 1rem; }
    .msg-detail-header, .msg-detail-subject, .msg-detail-body, .msg-detail-footer { padding-left: 1.25rem; padding-right: 1.25rem; }
    .msg-detail-subject h2 { font-size: 1.25rem; }
}
</style>
@endsection
