@extends('compagnie.layouts.template')

@section('styles')
<style>
    .message-container { max-width: 900px; margin: 0 auto; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-3); font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 24px; transition: color 0.2s; }
    .btn-back:hover { color: var(--orange); text-decoration: none; }

    .detail-card { background: var(--surface); border-radius: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-md); overflow: hidden; }
    
    .meta-section { padding: 32px; border-bottom: 1px solid var(--border); background: linear-gradient(135deg, var(--surface-2), var(--surface)); display: flex; justify-content: space-between; gap: 24px; flex-wrap: wrap; }
    
    .meta-left { display: flex; gap: 20px; align-items: flex-start; }
    .sender-avatar { width: 64px; height: 64px; background: white; border: 1px solid #A7F3D0; color: #059669; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; box-shadow: var(--shadow-sm); flex-shrink: 0; }
    
    .meta-info { flex: 1; }
    .meta-tags { display: flex; align-items: center; gap: 12px; margin-bottom: 8px; }
    .tag-role { background: #ECFDF5; color: #059669; font-size: 9px; font-weight: 800; text-transform: uppercase; padding: 4px 8px; border-radius: 6px; border: 1px solid #A7F3D0; }
    .meta-date { font-size: 12px; font-weight: 700; color: var(--text-3); }
    
    .meta-title { font-size: 24px; font-weight: 800; color: var(--text-1); margin-bottom: 4px; line-height: 1.2; }
    .meta-sender { font-size: 13px; color: var(--text-2); font-weight: 500; }
    .meta-sender strong { color: var(--text-1); font-weight: 700; }

    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 20px; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-badge.read { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .status-badge.new { background: #FFF1F2; color: #E11D48; border: 1px solid #FECDD3; animation: pulseRed 2s infinite; }

    .message-body { padding: 40px; background: var(--surface); position: relative; }
    .quote-icon { position: absolute; top: 30px; left: 30px; font-size: 80px; color: var(--surface-2); opacity: 0.6; z-index: 0; }
    .message-text { position: relative; z-index: 1; padding-left: 24px; border-left: 4px solid var(--border-strong); font-size: 15px; line-height: 1.8; color: var(--text-1); white-space: pre-wrap; font-weight: 500; }

    .detail-footer { padding: 16px 32px; background: var(--surface-2); border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .msg-id { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 1px; }
    .btn-print { background: transparent; border: none; color: var(--text-3); font-size: 16px; cursor: pointer; transition: 0.2s; }
    .btn-print:hover { color: var(--orange); }

    @keyframes pulseRed { 0% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(225, 29, 72, 0); } 100% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); } }

    @media (max-width: 640px) {
        .meta-section { padding: 24px; flex-direction: column; }
        .message-body { padding: 24px; }
        .message-text { padding-left: 16px; font-size: 14px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="message-container">
        <a href="{{ route('compagnie.messages.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>

        <div class="detail-card">
            <div class="meta-section">
                <div class="meta-left">
                    <div class="sender-avatar">
                        <i class="fas {{ $message->sender_icon ?? 'fa-warehouse' }}"></i>
                    </div>
                    <div class="meta-info">
                        <div class="meta-tags">
                            <span class="tag-role">{{ $message->sender_type_label ?? 'Gare' }}</span>
                            <span class="meta-date">{{ $message->created_at->isoFormat('LLLL') }}</span>
                        </div>
                        <h1 class="meta-title">{{ $message->subject }}</h1>
                        <div class="meta-sender">De: <strong>{{ $message->sender_name ?? 'La Gare' }}</strong></div>
                    </div>
                </div>
                
                <div class="meta-right">
                    @if($message->is_read)
                    <div class="status-badge read">
                        <i class="fas fa-check-double"></i> Lu le {{ $message->updated_at->format('d/m/Y') }}
                    </div>
                    @else
                    <div class="status-badge new">
                        Nouveau Message
                    </div>
                    @endif
                </div>
            </div>

            <div class="message-body">
                <i class="fas fa-quote-left quote-icon"></i>
                <div class="message-text">{!! $message->message !!}</div>
            </div>

            <div class="detail-footer print-hide">
                <span class="msg-id">ID: #{{ str_pad($message->id, 5, '0', STR_PAD_LEFT) }}</span>
                <button class="btn-print" onclick="window.print()" title="Imprimer le message">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .dashboard-page { padding: 0 !important; background: white !important; }
        .btn-back, .topbar, .sidebar, .print-hide { display: none !important; }
        .detail-card { box-shadow: none !important; border: 1px solid #ddd !important; border-radius: 0 !important; }
        .quote-icon { display: none !important; }
        .message-text { border-left: none; padding-left: 0; }
    }
</style>
@endsection