@extends('compagnie.layouts.template')

@section('page-title', 'Communication')
@section('page-subtitle', 'Espace d\'échange professionnel Car225')

@section('styles')
<style>
    /* Headers & Actions */
    .msg-header-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 24px; }
    .btn-new-msg { background: var(--orange); color: white; padding: 12px 20px; border-radius: 12px; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; transition: all 0.2s; box-shadow: 0 8px 16px rgba(249,115,22,0.25); }
    .btn-new-msg:hover { background: var(--orange-dark); color: white; text-decoration: none; transform: translateY(-2px); box-shadow: 0 12px 20px rgba(249,115,22,0.35); }

    /* Tabs */
    .msg-tabs { display: flex; gap: 8px; margin-bottom: 0; border-bottom: none; }
    .msg-tab-btn { padding: 14px 24px; font-weight: 800; font-size: 13px; color: var(--text-3); background: transparent; border: 1px solid transparent; border-bottom: none; border-radius: 16px 16px 0 0; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s; }
    .msg-tab-btn:hover { color: var(--text-2); background: rgba(255,255,255,0.5); }
    .msg-tab-btn.active { background: var(--surface); color: var(--orange); border-color: var(--border); box-shadow: 0 -4px 15px rgba(0,0,0,0.02); }
    
    .msg-badge { background: var(--surface-2); color: var(--text-2); min-width: 22px; height: 22px; display: inline-flex; align-items: center; justify-content: center; border-radius: 20px; font-size: 11px; font-weight: 800; padding: 0 6px; }
    .msg-tab-btn.active .msg-badge { background: var(--orange-light); color: var(--orange); }
    .msg-badge-danger { background: #FEF2F2 !important; color: #DC2626 !important; border: 1px solid #FECDD3; }

    /* Panels & Filters */
    .msg-panel { background: var(--surface); border-radius: 0 16px 16px 16px; border: 1px solid var(--border); box-shadow: var(--shadow-sm); min-height: 400px; display: flex; flex-direction: column; overflow: hidden; }
    .msg-filter-bar { padding: 12px 16px; border-bottom: 1px solid var(--border); background: var(--surface-2); display: flex; gap: 8px; overflow-x: auto; scrollbar-width: none; }
    .msg-filter-pill { padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; color: var(--text-3); text-decoration: none; display: flex; align-items: center; gap: 8px; transition: all 0.2s; white-space: nowrap; border: 1px solid transparent; }
    .msg-filter-pill:hover { color: var(--text-2); text-decoration: none; }
    .msg-filter-pill.active { background: var(--surface); color: var(--text-1); border-color: var(--border); box-shadow: var(--shadow-sm); }

    /* Message List */
    .msg-list { flex: 1; padding: 12px; display: flex; flex-direction: column; gap: 4px; }
    .msg-item { display: flex; align-items: center; gap: 16px; padding: 16px; border-radius: 12px; border: 1px solid transparent; background: transparent; transition: all 0.2s; text-decoration: none !important; color: inherit; }
    .msg-item:hover { background: var(--surface-2); border-color: var(--border); transform: translateX(6px); box-shadow: var(--shadow-sm); }
    
    .msg-avatar { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800; flex-shrink: 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .av-orange { background: var(--orange-light); color: var(--orange); border: 1px solid var(--orange-mid); }
    .av-blue { background: #EFF6FF; color: #2563EB; border: 1px solid #BFDBFE; }
    .av-purple { background: #F3E8FF; color: #9333EA; border: 1px solid #E9D5FF; }
    .av-emerald { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    
    .msg-content { flex: 1; min-width: 0; }
    .msg-sender-wrap { display: flex; align-items: center; gap: 8px; margin-bottom: 4px; }
    .msg-sender { font-size: 14px; font-weight: 800; color: var(--text-1); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .msg-tag { font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 2px 6px; border-radius: 6px; }
    
    .msg-subject { font-size: 13px; font-weight: 700; color: var(--text-1); margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .msg-item.read .msg-subject { color: var(--text-2); font-weight: 600; }
    .msg-snippet { font-size: 12px; color: var(--text-3); font-style: italic; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    
    .msg-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; flex-shrink: 0; min-width: 100px; }
    .msg-time { font-size: 11px; font-weight: 700; color: var(--text-3); }
    .msg-status { padding: 4px 10px; border-radius: 20px; font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px; }
    .st-read { background: #ECFDF5; color: #059669; border: 1px solid #A7F3D0; }
    .st-unread { background: #FFF1F2; color: #E11D48; border: 1px solid #FECDD3; animation: pulseRed 2s infinite; }
    .st-sent { background: var(--surface-2); color: var(--text-2); border: 1px solid var(--border); }

    .msg-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px; text-align: center; }
    .msg-empty-icon { width: 80px; height: 80px; border-radius: 50%; background: var(--surface-2); display: flex; align-items: center; justify-content: center; font-size: 32px; color: var(--text-3); margin-bottom: 16px; }
    .msg-empty-title { font-size: 18px; font-weight: 800; color: var(--text-1); margin-bottom: 8px; }
    .msg-empty-text { font-size: 13px; color: var(--text-3); }

    @keyframes pulseRed { 0% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0.4); } 70% { box-shadow: 0 0 0 6px rgba(225, 29, 72, 0); } 100% { box-shadow: 0 0 0 0 rgba(225, 29, 72, 0); } }

    @media (max-width: 768px) {
        .msg-item { flex-direction: column; align-items: flex-start; gap: 12px; position: relative; }
        .msg-meta { flex-direction: row; justify-content: space-between; width: 100%; align-items: center; }
        .msg-time { position: absolute; top: 16px; right: 16px; }
    }
</style>
@endsection

@section('content')
@php
    // Si l'URL contient "type" ou "tab=sent", c'est qu'on navigue dans les messages envoyés
    $isSentActive = request()->has('type') || request('tab') == 'sent';
@endphp
<div class="dashboard-page">

    <div class="msg-header-actions">
        <div>
            <h2 class="dash-title">Communication</h2>
            <p class="dash-subtitle">Espace d'échange professionnel Car225</p>
        </div>
        <a href="{{ route('compagnie.messages.create') }}" class="btn-new-msg">
            <i class="fas fa-plus-circle"></i> Nouveau Message
        </a>
    </div>

    <!-- Main Tabs -->
   <div class="msg-tabs">
        <button class="msg-tab-btn {{ !$isSentActive && request('tab') !== 'accident' ? 'active' : '' }}" onclick="switchMainTab('received', this)">
            <i class="fas fa-inbox"></i> Reçus (Gares)
            <span class="msg-badge {{ $unreadReceivedCount > 0 ? 'msg-badge-danger' : '' }}">
                {{ $unreadReceivedCount > 0 ? $unreadReceivedCount : $receivedMessages->total() }}
            </span>
        </button>
        <button class="msg-tab-btn {{ request('tab') === 'accident' ? 'active' : '' }}" onclick="switchMainTab('accident', this)">
            <i class="fas fa-car-crash"></i> Bilans Accidents
            <span class="msg-badge {{ ($unreadAccidentCount ?? 0) > 0 ? 'msg-badge-danger' : '' }}">
                {{ ($unreadAccidentCount ?? 0) > 0 ? $unreadAccidentCount : ($accidentMessages->total() ?? 0) }}
            </span>
        </button>
        <button class="msg-tab-btn {{ $isSentActive ? 'active' : '' }}" onclick="switchMainTab('sent', this)">
            <i class="fas fa-paper-plane"></i> Envoyés
            <span class="msg-badge">{{ $messages->total() }}</span>
        </button>
    </div>

    <!-- PANEL: ACCIDENT BILANS -->
    <div id="panel-accident" class="msg-panel" style="display: {{ request('tab') === 'accident' ? 'flex' : 'none' }};">
        <div class="msg-filter-bar">
            <span class="msg-filter-pill active" style="pointer-events: none;">
                <i class="fas fa-fire-extinguisher" style="color:#DC2626;"></i> Bilans d'intervention — Sapeurs Pompiers
            </span>
        </div>

        <div class="msg-list">
            @forelse($accidentMessages as $accMsg)
                <a href="{{ route('compagnie.messages.show', $accMsg->id) }}" class="msg-item {{ $accMsg->is_read ? 'read' : '' }}">
                    <div class="msg-avatar" style="background:{{ $accMsg->is_read ? '#FEE2E2' : '#DC2626' }};color:{{ $accMsg->is_read ? '#DC2626' : 'white' }};">
                        <i class="fas fa-car-crash"></i>
                    </div>
                    <div class="msg-content">
                        <div class="msg-sender-wrap">
                            <span class="msg-sender">Sapeurs Pompiers</span>
                            <span class="msg-tag" style="background:#FEE2E2;color:#DC2626;">Accident</span>
                        </div>
                        <div class="msg-subject">{{ $accMsg->subject }}</div>
                        <div class="msg-snippet">{{ Str::limit(strip_tags($accMsg->message), 80) }}</div>
                    </div>
                    <div class="msg-meta">
                        <span class="msg-time">{{ $accMsg->created_at->translatedFormat('d M, H:i') }}</span>
                        @if($accMsg->is_read)
                            <span class="msg-status st-read"><i class="fas fa-check-double"></i> Lu</span>
                        @else
                            <span class="msg-status st-unread"><i class="fas fa-envelope"></i> Nouveau</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="msg-empty">
                    <div class="msg-empty-icon"><i class="fas fa-shield-alt"></i></div>
                    <div class="msg-empty-title">Aucun bilan reçu</div>
                    <div class="msg-empty-text">Aucun rapport d'accident transmis par les sapeurs pompiers.</div>
                </div>
            @endforelse
        </div>

        @if(isset($accidentMessages) && $accidentMessages->hasPages())
        <div class="dash-card-footer">
            {{ $accidentMessages->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <!-- PANEL: RECEIVED MESSAGES -->
    <div id="panel-received" class="msg-panel" style="border-top-left-radius: 0; display: {{ !$isSentActive && request('tab') !== 'accident' ? 'flex' : 'none' }};">
        <div class="msg-filter-bar">
            <span class="msg-filter-pill active" style="pointer-events: none;">
                <i class="fas fa-warehouse text-orange"></i> Boîte de réception Gares
            </span>
        </div>

        <div class="msg-list">
            @forelse($receivedMessages as $gareMsg)
                <a href="{{ route('compagnie.messages.show-received', $gareMsg->id) }}" class="msg-item {{ $gareMsg->is_read ? 'read' : '' }}">
                    <div class="msg-avatar av-orange">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    
                    <div class="msg-content">
                        <div class="msg-sender-wrap">
                            <span class="msg-sender">{{ $gareMsg->gare->nom_gare ?? 'Gare Inconnue' }}</span>
                            <span class="msg-tag av-orange">Gare</span>
                        </div>
                        <div class="msg-subject">{{ $gareMsg->subject }}</div>
                        <div class="msg-snippet">{{ $gareMsg->message }}</div>
                    </div>

                    <div class="msg-meta">
                        <span class="msg-time">{{ $gareMsg->created_at->translatedFormat('d M, H:i') }}</span>
                        @if($gareMsg->is_read)
                            <span class="msg-status st-read"><i class="fas fa-check-double"></i> Lu</span>
                        @else
                            <span class="msg-status st-unread"><i class="fas fa-envelope"></i> Nouveau</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="msg-empty">
                    <div class="msg-empty-icon"><i class="fas fa-inbox"></i></div>
                    <div class="msg-empty-title">Aucun message reçu</div>
                    <div class="msg-empty-text">Vos gares ne vous ont envoyé aucun message pour le moment.</div>
                </div>
            @endforelse
        </div>

        @if($receivedMessages->hasPages())
        <div class="dash-card-footer">
            {{ $receivedMessages->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

    <!-- PANEL: SENT MESSAGES -->
  <div id="panel-sent" class="msg-panel" style="display: {{ $isSentActive ? 'flex' : 'none' }};">
       <div class="msg-filter-bar">
            <a class="msg-filter-pill {{ !request('type') ? 'active' : '' }}" href="{{ route('compagnie.messages.index', ['tab' => 'sent']) }}">
                <i class="fas fa-layer-group"></i> Flux global
            </a>
            <a class="msg-filter-pill {{ request('type') == 'agent' ? 'active' : '' }}" href="{{ route('compagnie.messages.index', ['type' => 'agent', 'tab' => 'sent']) }}">
                <i class="fas fa-user-tie"></i> Agents
            </a>
            <a class="msg-filter-pill {{ request('type') == 'personnel' ? 'active' : '' }}" href="{{ route('compagnie.messages.index', ['type' => 'personnel', 'tab' => 'sent']) }}">
                <i class="fas fa-steering-wheel"></i> Chauffeurs
            </a>
            <a class="msg-filter-pill {{ request('type') == 'caisse' ? 'active' : '' }}" href="{{ route('compagnie.messages.index', ['type' => 'caisse', 'tab' => 'sent']) }}">
                <i class="fas fa-cash-register"></i> Points Caisse
            </a>
        </div>

        <div class="msg-list">
            @forelse($messages as $message)
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

                    $typeConfig = [
                        'App\\Models\\Agent' => ['color' => 'av-blue', 'label' => 'Agent'],
                        'App\\Models\\Caisse' => ['color' => 'av-emerald', 'label' => 'Caisse'],
                        'App\\Models\\Personnel' => ['color' => 'av-purple', 'label' => 'Chauffeur'],
                        'App\\Models\\Gare' => ['color' => 'av-orange', 'label' => 'Gare'],
                    ][$message->recipient_type] ?? ['color' => 'av-blue', 'label' => 'Inconnu'];
                @endphp

                <a href="{{ route('compagnie.messages.show', $message->id) }}" class="msg-item read">
                    <div class="msg-avatar {{ $typeConfig['color'] }}">
                        {{ $initials }}
                    </div>
                    
                    <div class="msg-content">
                        <div class="msg-sender-wrap">
                            <span class="msg-sender">{{ $recipientName }}</span>
                            <span class="msg-tag {{ $typeConfig['color'] }}">{{ $typeConfig['label'] }}</span>
                        </div>
                        <div class="msg-subject">{{ $message->subject }}</div>
                        <div class="msg-snippet">{{ $message->message }}</div>
                    </div>

                    <div class="msg-meta">
                        <span class="msg-time">{{ $message->created_at->translatedFormat('d M, H:i') }}</span>
                        @if($message->is_read)
                            <span class="msg-status st-read"><i class="fas fa-check-double"></i> Lu</span>
                        @else
                            <span class="msg-status st-sent"><i class="fas fa-paper-plane"></i> Envoyé</span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="msg-empty">
                    <div class="msg-empty-icon"><i class="fas fa-paper-plane"></i></div>
                    <div class="msg-empty-title">Aucune communication envoyée</div>
                    <div class="msg-empty-text">Vous n'avez envoyé aucun message dans cette catégorie.</div>
                    <br>
                    <a href="{{ route('compagnie.messages.create') }}" class="btn-action btn-secondary" style="border-radius:12px; padding:10px 20px;">
                        Envoyer le premier message
                    </a>
                </div>
            @endforelse
        </div>

        @if($messages->hasPages())
        <div class="dash-card-footer">
            {{ $messages->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
        @endif
    </div>

</div>

@endsection

@section('scripts')
<script>
function switchMainTab(tab, btn) {
    document.getElementById('panel-sent').style.display = tab === 'sent' ? 'flex' : 'none';
    document.getElementById('panel-received').style.display = tab === 'received' ? 'flex' : 'none';
    document.getElementById('panel-accident').style.display = tab === 'accident' ? 'flex' : 'none';
    
    document.querySelectorAll('.msg-tab-btn').forEach(b => {
        b.classList.remove('active');
        b.style.borderBottom = 'none';
    });
    btn.classList.add('active');
}
</script>
@endsection