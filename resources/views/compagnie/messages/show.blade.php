@extends('compagnie.layouts.template')

@section('styles')
<style>
    .message-container { max-width: 900px; margin: 0 auto; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-3); font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 24px; transition: color 0.2s; }
    .btn-back:hover { color: var(--orange); text-decoration: none; }

    .detail-card { background: var(--surface); border-radius: 24px; border: 1px solid var(--border); box-shadow: var(--shadow-md); overflow: hidden; }
    
    .top-bar { padding: 24px 32px; border-bottom: 1px solid var(--border); background: var(--surface); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
    .top-left { display: flex; align-items: center; gap: 16px; }
    .icon-box { width: 50px; height: 50px; border-radius: 14px; background: var(--orange-light); color: var(--orange); border: 1px solid var(--orange-mid); display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .msg-title { font-size: 20px; font-weight: 800; color: var(--text-1); margin-bottom: 4px; line-height: 1.2; }
    .msg-date { font-size: 12px; font-weight: 600; color: var(--text-3); display: flex; align-items: center; gap: 6px; }
    
    .status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
    .st-read { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .st-wait { background: var(--surface-2); color: var(--text-2); border: 1px solid var(--border); }

    .recipient-panel { padding: 24px 32px; background: var(--surface-2); border-bottom: 1px solid var(--border); }
    .recipient-chip { background: var(--surface); border: 1px solid var(--border); border-radius: 16px; padding: 16px 24px; display: inline-flex; align-items: center; gap: 16px; box-shadow: var(--shadow-sm); }
    
    .rc-avatar { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 800; color: white; }
    .bg-blue { background: #2563EB; }
    .bg-emerald { background: #059669; }
    .bg-purple { background: #9333EA; }
    .bg-orange { background: var(--orange); }
    .bg-slate { background: var(--text-2); }
    
    .rc-label { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-3); margin-bottom: 2px; display: block; }
    .rc-name { font-size: 16px; font-weight: 800; color: var(--text-1); margin-bottom: 2px; line-height: 1.2; }
    .rc-role { font-size: 11px; font-weight: 700; color: var(--text-2); }

    .message-body { padding: 40px 32px; background: var(--surface); }
    .message-bubble { background: var(--surface-2); border-radius: 20px; padding: 32px; position: relative; border: 1px solid var(--border); }
    .quote-mark { position: absolute; top: 10px; left: 20px; font-size: 80px; font-family: serif; color: var(--border-strong); opacity: 0.3; line-height: 1; pointer-events: none; }
    .message-text { position: relative; z-index: 1; font-size: 15px; line-height: 1.8; color: var(--text-1); white-space: pre-wrap; font-weight: 500; }

    .detail-footer { padding: 16px 32px; background: var(--surface-2); border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
    .msg-id { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 1px; }
    .btn-action { background: var(--surface); border: 1px solid var(--border); color: var(--text-2); padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-action:hover { background: var(--border); color: var(--text-1); }

    @media (max-width: 640px) {
        .top-bar, .recipient-panel, .message-body { padding: 20px; }
        .message-bubble { padding: 20px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="message-container">
        <a href="{{ route('compagnie.messages.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour aux messages
        </a>

        <div class="detail-card">
            <div class="top-bar">
                <div class="top-left">
                    <div class="icon-box">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h1 class="msg-title">{{ $message->subject }}</h1>
                        <div class="msg-date">
                            <i class="far fa-clock"></i> Envoyé le {{ $message->created_at->translatedFormat('d F Y \à H:i') }}
                        </div>
                    </div>
                </div>

                <div class="top-right">
                    @if($message->is_read)
                        <span class="status-pill st-read">
                            <span style="width:6px; height:6px; background:#059669; border-radius:50%;"></span> MESSAGE CONSULTÉ
                        </span>
                    @else
                        <span class="status-pill st-wait">
                            <span style="width:6px; height:6px; background:var(--text-3); border-radius:50%;"></span> EN ATTENTE DE LECTURE
                        </span>
                    @endif
                </div>
            </div>

            <div class="recipient-panel">
                @php
                    $recipient = $message->recipient;
                    $recipientName = 'Indéfini';
                    $initials = '??';
                    
                    if ($recipient) {
                        if ($message->recipient_type === 'App\Models\Gare') {
                            $recipientName = $recipient->nom_gare;
                            $initials = strtoupper(substr($recipient->nom_gare, 0, 2));
                        } else {
                            $recipientName = trim(($recipient->name ?? '') . ' ' . ($recipient->prenom ?? ''));
                            $initials = strtoupper(substr($recipient->name ?? '?', 0, 1) . substr($recipient->prenom ?? '', 0, 1));
                        }
                    }

                    $typeMeta = [
                        'App\\Models\\Agent' => ['label' => 'Agent Commercial', 'bg' => 'bg-blue'],
                        'App\\Models\\Caisse' => ['label' => 'Point de Caisse', 'bg' => 'bg-emerald'],
                        'App\\Models\\Personnel' => ['label' => 'Chauffeur / Personnel', 'bg' => 'bg-purple'],
                        'App\\Models\\Gare' => ['label' => 'Gare', 'bg' => 'bg-orange'],
                    ][$message->recipient_type] ?? ['label' => 'Utilisateur', 'bg' => 'bg-slate'];
                @endphp

                <div class="recipient-chip">
                    <div class="rc-avatar {{ $typeMeta['bg'] }}">
                        {{ $initials }}
                    </div>
                    <div>
                        <span class="rc-label">Destinataire :</span>
                        <h4 class="rc-name">{{ $recipientName }}</h4>
                        <span class="rc-role">{{ $typeMeta['label'] }}</span>
                    </div>
                </div>
            </div>

            <div class="message-body">
                <div class="message-bubble">
                    <div class="quote-mark">"</div>
                    <div class="message-text">{!! nl2br(preg_replace(
                        '/(https?:\/\/[^\s\)]+)/',
                        '<a href="$1" target="_blank" style="color:#2563EB;font-weight:700;text-decoration:underline;word-break:break-all;">$1</a>',
                        e($message->message)
                    )) !!}</div>
                </div>

                {{-- Bouton d'action si c'est un bilan d'accident avec lien de notification --}}
                @if(Str::contains($message->message, '/notification-accident'))
                    @php
                        preg_match('/(https?:\/\/[^\s\)]+notification-accident)/', $message->message, $matches);
                        $notifLink = $matches[1] ?? null;
                        // Extraire l'ID du signalement depuis le lien
                        preg_match('/signalements\/(\d+)\/notification-accident/', $message->message, $idMatches);
                        $sigId = $idMatches[1] ?? null;
                    @endphp
                    @if($sigId)
                    <div style="margin-top:20px;padding:20px;background:linear-gradient(135deg,#EFF6FF,#DBEAFE);border:1px solid #93C5FD;border-radius:16px;display:flex;align-items:center;gap:16px;">
                        <div style="width:48px;height:48px;background:white;border-radius:14px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(37,99,235,0.15);">
                            <i class="fas fa-paper-plane" style="color:#2563EB;font-size:18px;"></i>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:13px;font-weight:800;color:#1E40AF;margin-bottom:2px;">Notifier les contacts d'urgence</div>
                            <div style="font-size:11px;color:#3B82F6;font-weight:600;">Accédez à la page de notification pour informer les familles des passagers évacués.</div>
                        </div>
                        <a href="{{ route('compagnie.signalement.notification-accident', $sigId) }}"
                           style="background:#2563EB;color:white;padding:10px 20px;border-radius:12px;font-size:12px;font-weight:800;text-decoration:none;display:inline-flex;align-items:center;gap:8px;white-space:nowrap;box-shadow:0 4px 12px rgba(37,99,235,0.3);transition:all 0.2s;">
                            <i class="fas fa-arrow-right"></i> Accéder
                        </a>
                    </div>
                    @endif
                @endif
            </div>

            <div class="detail-footer print-hide">
                <span class="msg-id">ID: #MSG-{{ str_pad($message->id, 5, '0', STR_PAD_LEFT) }}</span>
                <button onclick="window.print()" class="btn-action">
                    <i class="fas fa-print"></i> Imprimer
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
        .message-bubble { background: white !important; border: 1px solid #eee !important; }
    }
</style>
@endsection