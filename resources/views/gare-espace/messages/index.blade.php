@extends('gare-espace.layouts.template')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="msg-wrapper">
    <!-- Header -->
    <div class="msg-header animate__animated animate__fadeIn">
        <div>
            <h1 class="msg-title">Boîte de réception</h1>
            <p class="msg-subtitle">Gérez vos communications avec vos équipes et la compagnie</p>
        </div>
        <a href="{{ route('gare-espace.messages.create') }}" class="btn-compose">
            <i class="fas fa-pen-fancy"></i>
            <span>Nouveau message</span>
        </a>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs animate__animated animate__fadeIn" style="animation-delay: 0.1s">
        <a href="{{ route('gare-espace.messages.index') }}" class="filter-tab {{ !request('type') || request('type') === 'all' ? 'active' : '' }}">
            <i class="fas fa-inbox"></i> Tous
        </a>
        <a href="{{ route('gare-espace.messages.index', ['type' => 'agent']) }}" class="filter-tab {{ request('type') === 'agent' ? 'active' : '' }}">
            <i class="fas fa-user-tie"></i> Agents
        </a>
        <a href="{{ route('gare-espace.messages.index', ['type' => 'caisse']) }}" class="filter-tab {{ request('type') === 'caisse' ? 'active' : '' }}">
            <i class="fas fa-cash-register"></i> Caisse
        </a>
        <a href="{{ route('gare-espace.messages.index', ['type' => 'personnel']) }}" class="filter-tab {{ request('type') === 'personnel' ? 'active' : '' }}">
            <i class="fas fa-truck"></i> Chauffeurs
        </a>
        <a href="{{ route('gare-espace.messages.index', ['type' => 'compagnie']) }}" class="filter-tab {{ request('type') === 'compagnie' ? 'active' : '' }}">
            <i class="fas fa-building"></i> Compagnie
        </a>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-6 relative z-10">
        <div class="flex bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm transition-all">
            <button class="flex-1 py-3.5 px-6 rounded-xl font-bold transition-all flex items-center justify-center gap-3 tab-btn" 
                    id="received-trigger" onclick="switchTab('received')">
                <i class="fas fa-inbox text-xl"></i>
                <div class="flex items-center gap-2">
                    <span>Messages Reçus (Direction)</span>
                    @php $unreadCount = $receivedMessages->where('is_read', false)->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="flex items-center justify-center bg-red-500 text-white text-[11px] min-w-[22px] h-[22px] px-1.5 rounded-full shadow-md animate-pulse">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </div>
            </button>
            <button class="flex-1 py-3.5 px-6 rounded-xl font-bold transition-all flex items-center justify-center gap-3 text-slate-500 hover:bg-slate-50 tab-btn" 
                    id="sent-trigger" onclick="switchTab('sent')">
                <i class="fas fa-paper-plane text-xl"></i>
                <span>Messages Envoyés</span>
            </button>
            <button class="flex-1 py-3.5 px-6 rounded-xl font-bold transition-all flex items-center justify-center gap-3 text-slate-500 hover:bg-slate-50 tab-btn" 
                    id="staff-trigger" onclick="switchTab('staff')">
                <i class="fas fa-users text-xl"></i>
                <div class="flex items-center gap-2">
                    <span>Du Personnel</span>
                    @php $staffUnread = $staffMessages->where('is_read', false)->count(); @endphp
                    @if($staffUnread > 0)
                        <span class="flex items-center justify-center bg-blue-500 text-white text-[11px] min-w-[22px] h-[22px] px-1.5 rounded-full shadow-md animate-pulse">
                            {{ $staffUnread }}
                        </span>
                    @endif
                </div>
            </button>
        </div>
    </div>

    <div class="tab-content" id="message-panels">
        <!-- Received Messages Tab (Default) -->
        <div class="tab-panel animate__animated animate__fadeIn" id="received-panel">
            <div class="msg-card animate__animated animate__fadeInUp">
                @if($receivedMessages->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                        <h3>Aucun message reçu</h3>
                        <p>Vous n'avez reçu aucun message de la direction.</p>
                    </div>
                @else
                    <div class="msg-list">
                        @foreach($receivedMessages as $msg)
                            <a href="{{ route('gare-espace.messages.show', ['id' => $msg->id, 'type' => 'received']) }}" class="msg-item {{ !$msg->is_read ? 'unread' : '' }}">
                                <div class="msg-item-left">
                                    <div class="msg-avatar bg-slate-800">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="msg-content">
                                        <div class="msg-recipient">
                                            {{ $msg->compagnie->name ?? 'La Direction' }}
                                            <span class="msg-badge">Compagnie</span>
                                        </div>
                                        <p class="msg-subject">{{ $msg->subject }}</p>
                                        <p class="msg-preview text-slate-400">{{ Str::limit($msg->message, 80) }}</p>
                                    </div>
                                </div>
                                <div class="msg-item-right">
                                    <span class="msg-date">{{ $msg->created_at->diffForHumans() }}</span>
                                    <i class="fas fa-chevron-right msg-arrow"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="p-4">{{ $receivedMessages->appends(request()->query())->links() }}</div>
                @endif
            </div>
        </div>

        <!-- Sent Messages Tab -->
        <div class="tab-panel hidden animate__animated animate__fadeIn" id="sent-panel">
            <div class="msg-card animate__animated animate__fadeInUp">
                @if($sentMessages->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-paper-plane"></i></div>
                        <h3>Aucun message envoyé</h3>
                        <p>Vous n'avez envoyé aucun message pour le moment.</p>
                    </div>
                @else
                    <div class="msg-list">
                        @foreach($sentMessages as $msg)
                            <a href="{{ route('gare-espace.messages.show', $msg->id) }}" class="msg-item">
                                <div class="msg-item-left">
                                    <div class="msg-avatar">
                                        @php
                                            $initials = '??';
                                            if($msg->recipient) {
                                                if($msg->recipient_type === 'App\Models\Compagnie') {
                                                    $initials = strtoupper(substr($msg->recipient->name ?? 'C', 0, 2));
                                                } else {
                                                    $initials = strtoupper(substr($msg->recipient->name ?? '', 0, 1)) . strtoupper(substr($msg->recipient->prenom ?? '', 0, 1));
                                                }
                                            }
                                        @endphp
                                        {{ $initials }}
                                    </div>
                                    <div class="msg-content">
                                        <div class="msg-recipient">
                                            @if($msg->recipient)
                                                {{ $msg->recipient->name ?? $msg->recipient->nom_gare }} {{ $msg->recipient->prenom ?? '' }}
                                            @else Destinataire inconnu @endif
                                            <span class="msg-badge">{{ $msg->recipient_type_label }}</span>
                                        </div>
                                        <p class="msg-subject">{{ $msg->subject }}</p>
                                        <p class="msg-preview text-slate-400">{{ Str::limit($msg->message, 80) }}</p>
                                    </div>
                                </div>
                                <div class="msg-item-right">
                                    <span class="msg-date">{{ $msg->created_at->diffForHumans() }}</span>
                                    <i class="fas fa-chevron-right msg-arrow"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="p-4">{{ $sentMessages->links() }}</div>
                @endif
            </div>
        </div>

        <!-- Staff Messages Tab -->
        <div class="tab-panel hidden animate__animated animate__fadeIn" id="staff-panel">
            <div class="msg-card animate__animated animate__fadeInUp">
                @if($staffMessages->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-users"></i></div>
                        <h3>Aucun message du personnel</h3>
                        <p>Vos chauffeurs et agents n'ont envoyé aucun message pour le moment.</p>
                    </div>
                @else
                    <div class="msg-list">
                        @foreach($staffMessages as $msg)
                            @php
                                $senderName = 'Inconnu';
                                $senderBadge = 'Personnel';
                                if ($msg->sender_type === 'App\Models\Personnel') {
                                    $sender = \App\Models\Personnel::find($msg->sender_id);
                                    $senderName = $sender ? ($sender->prenom . ' ' . $sender->name) : 'Chauffeur inconnu';
                                    $senderBadge = 'Chauffeur';
                                } elseif ($msg->sender_type === 'App\Models\Agent') {
                                    $sender = \App\Models\Agent::find($msg->sender_id);
                                    $senderName = $sender ? ($sender->prenom . ' ' . $sender->name) : 'Agent inconnu';
                                    $senderBadge = 'Agent';
                                }
                                $initials = strtoupper(substr($senderName, 0, 1)) . strtoupper(substr(explode(' ', $senderName)[1] ?? '', 0, 1));
                            @endphp
                            <div class="msg-item staff-msg-toggle {{ !$msg->is_read ? 'unread' : '' }}" data-msg-id="{{ $msg->id }}" style="cursor: pointer; flex-wrap: wrap;">
                                <div class="msg-item-left">
                                    <div class="msg-avatar" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                                        {{ $initials }}
                                    </div>
                                    <div class="msg-content">
                                        <div class="msg-recipient">
                                            {{ $senderName }}
                                            <span class="msg-badge" style="background: #dbeafe; color: #2563eb;">{{ $senderBadge }}</span>
                                            @if(!$msg->is_read)
                                                <span class="msg-badge new-badge" style="background: #fef2f2; color: #ef4444;">Nouveau</span>
                                            @endif
                                        </div>
                                        <p class="msg-subject">{{ $msg->subject }}</p>
                                        <p class="msg-preview text-slate-400">{{ Str::limit($msg->message, 80) }}</p>
                                    </div>
                                </div>
                                <div class="msg-item-right">
                                    <span class="msg-date">{{ $msg->created_at->diffForHumans() }}</span>
                                    <i class="fas fa-chevron-down msg-arrow"></i>
                                </div>
                                <div class="staff-msg-detail hidden w-full mt-3 p-4 bg-slate-50 rounded-xl">
                                    <p class="text-sm text-slate-700">{{ $msg->message }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4">{{ $staffMessages->appends(request()->query())->links() }}</div>
                @endif
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

.msg-wrapper {
    padding: 2rem;
    font-family: var(--font-family);
    width: 100%;
    margin: 0 auto;
}

.msg-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.msg-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
}

.msg-subtitle {
    color: var(--text-muted);
    margin: 0.25rem 0 0;
}

.btn-compose {
    background: var(--primary);
    color: white !important;
    padding: 0.85rem 1.75rem;
    border-radius: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none !important;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(233, 79, 27, 0.2);
}

.btn-compose:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(233, 79, 27, 0.3);
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.filter-tab {
    padding: 0.6rem 1.25rem;
    border-radius: 2rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-muted);
    background: white;
    border: 1px solid var(--border-color);
    text-decoration: none !important;
    white-space: nowrap;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.filter-tab:hover {
    background: #f8fafc;
    color: var(--text-main);
}

.filter-tab.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

/* Message Card */
.msg-card {
    background: white;
    border-radius: 1.5rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
    overflow: hidden;
}

.msg-list {
    display: flex;
    flex-direction: column;
}

.msg-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.75rem;
    border-bottom: 1px solid #f1f5f9;
    text-decoration: none !important;
    transition: all 0.2s;
    gap: 1rem;
}

.msg-item:hover {
    background: #fafbfc;
}

.msg-item.unread {
    background: #fff7ed;
    border-left: 3px solid var(--primary);
}

.msg-item-left {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex: 1;
    min-width: 0;
}

.msg-avatar {
    width: 48px;
    height: 48px;
    border-radius: 1rem;
    background: linear-gradient(135deg, var(--primary), #ff6b3d);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    flex-shrink: 0;
}

.msg-content {
    min-width: 0;
}

.msg-recipient {
    font-weight: 700;
    color: var(--text-main);
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.msg-badge {
    background: #f1f5f9;
    color: var(--text-muted);
    padding: 0.15rem 0.6rem;
    border-radius: 1rem;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.msg-subject {
    font-weight: 600;
    color: var(--text-main);
    margin: 0.15rem 0;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.msg-preview {
    color: var(--text-muted);
    font-size: 0.8rem;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.msg-item-right {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-shrink: 0;
}

.msg-date {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 600;
    white-space: nowrap;
}

.msg-arrow {
    color: #cbd5e1;
    font-size: 0.75rem;
}

/* Empty State */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: #f1f5f9;
    border-radius: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    font-size: 2rem;
    color: #94a3b8;
}

.empty-state h3 {
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.5rem;
}

.empty-state p {
    color: var(--text-muted);
}

.msg-pagination {
    padding: 1rem 1.5rem;
    border-top: 1px solid #f1f5f9;
}

@media (max-width: 768px) {
    .msg-wrapper { padding: 1rem; }
    .msg-title { font-size: 1.5rem; }
    .msg-item { padding: 1rem; }
    .msg-item-right { display: none; }
}
.tab-btn.active {
    background: var(--primary);
    color: white !important;
}

.tab-btn:not(.active) {
    color: var(--text-muted);
}
</style>

@endsection

@section('scripts')
<script>
function switchTab(tabId) {
    const panels = ['received', 'sent', 'staff'];
    
    panels.forEach(id => {
        const panel = document.getElementById(`${id}-panel`);
        const trigger = document.getElementById(`${id}-trigger`);
        
        if (id === tabId) {
            if(panel) panel.classList.remove('hidden');
            if(trigger) {
                trigger.classList.add('active');
                trigger.style.background = '#e94f1b';
                trigger.style.color = 'white';
            }
        } else {
            if(panel) panel.classList.add('hidden');
            if(trigger) {
                trigger.classList.remove('active');
                trigger.style.background = 'transparent';
                trigger.style.color = '#64748b';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Déterminer l'onglet par défaut selon le filtre
    const urlParams = new URLSearchParams(window.location.search);
    const type = urlParams.get('type');
    
    // Si on filtre par agent/caisse/chauffeur, on va dans "Envoyés" par défaut
    if (['agent', 'caisse', 'personnel'].includes(type)) {
        switchTab('sent');
    } else {
        switchTab('received');
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès !',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

    // Staff messages: toggle detail + mark as read
    document.querySelectorAll('.staff-msg-toggle').forEach(function(el) {
        el.addEventListener('click', function() {
            var detail = this.querySelector('.staff-msg-detail');
            if (detail) detail.classList.toggle('hidden');

            if (this.classList.contains('unread')) {
                var msgId = this.dataset.msgId;
                this.classList.remove('unread');
                var newBadge = this.querySelector('.new-badge');
                if (newBadge) newBadge.remove();

                fetch('/gare-espace/messages/' + msgId + '/mark-read', {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
            }
        });
    });
});
</script>
@endsection
