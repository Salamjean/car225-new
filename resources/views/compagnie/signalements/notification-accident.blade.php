@extends('compagnie.layouts.template')

@section('page-title', 'Notification Accident')
@section('page-subtitle', 'Notifier les contacts d\'urgence des passagers évacués')

@section('styles')
<style>
    .notif-container { max-width: 900px; margin: 0 auto; }

    .notif-header {
        background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);
        border-radius: var(--radius); padding: 28px 32px; color: white; margin-bottom: 24px;
        position: relative; overflow: hidden; box-shadow: 0 8px 30px rgba(220,38,38,0.25);
    }
    .notif-header .bg-icon {
        position: absolute; right: 20px; top: 50%; transform: translateY(-50%);
        font-size: 90px; color: rgba(255,255,255,0.08); pointer-events: none;
    }
    .notif-header h1 { font-size: 20px; font-weight: 900; margin: 8px 0 6px; }
    .notif-header p { font-size: 12px; opacity: 0.85; font-weight: 600; margin: 0; }

    .stat-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 10px; font-size: 11px; font-weight: 800; }

    .hospital-block { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); margin-bottom: 20px; overflow: hidden; box-shadow: var(--shadow-sm); }
    .hospital-header { padding: 16px 24px; background: var(--surface-2); border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .hospital-icon { width: 42px; height: 42px; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #DC2626; flex-shrink: 0; }

    .passenger-row { display: flex; align-items: center; gap: 14px; padding: 14px 24px; border-bottom: 1px solid var(--border); transition: background 0.15s; }
    .passenger-row:last-child { border-bottom: none; }
    .passenger-row:hover { background: #FAFBFC; }

    .seat-badge { width: 36px; height: 36px; background: linear-gradient(135deg,#E0E7FF,#C7D2FE); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 13px; color: #4338CA; flex-shrink: 0; }

    .ice-chip { display: flex; align-items: center; gap: 10px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 8px 14px; }
    .ice-avatar { width: 32px; height: 32px; background: white; border: 1px solid #FCA5A5; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #DC2626; font-size: 12px; flex-shrink: 0; }

    .message-section { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-sm); }
    .message-section-header { padding: 20px 24px; background: linear-gradient(135deg,#EFF6FF,#DBEAFE); border-bottom: 1px solid #93C5FD; }
    .message-section-body { padding: 24px; }

    .btn-send {
        background: linear-gradient(135deg,#2563EB,#1D4ED8); color: white; border: none; padding: 14px 32px;
        border-radius: 14px; font-weight: 800; font-size: 13px; cursor: pointer; display: inline-flex;
        align-items: center; gap: 10px; box-shadow: 0 6px 20px rgba(37,99,235,0.3); transition: all 0.2s;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .btn-send:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(37,99,235,0.4); }

    .tag-hospital { display: inline-flex; align-items: center; gap: 6px; background: #EFF6FF; border: 1px solid #BFDBFE; color: #1D4ED8; padding: 6px 14px; border-radius: 8px; font-size: 11px; font-weight: 700; cursor: pointer; transition: 0.2s; border: none; }
    .tag-hospital:hover { background: #DBEAFE; }

    .custom-textarea { width: 100%; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 16px; font-size: 13px; font-weight: 500; line-height: 1.7; resize: vertical; min-height: 180px; font-family: inherit; color: var(--text-1); }
    .custom-textarea:focus { outline: none; border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.15); }

    .count-bar { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; }

    @media (max-width: 640px) {
        .notif-header { padding: 20px; }
        .hospital-header { padding: 14px 16px; }
        .passenger-row { padding: 12px 16px; flex-wrap: wrap; }
        .message-section-body { padding: 16px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="notif-container">

        {{-- Retour --}}
        <a href="{{ route('compagnie.signalements.show', $signalement->id) }}" class="btn-back" style="display:inline-flex;align-items:center;gap:8px;color:var(--text-3);font-weight:700;font-size:13px;text-decoration:none;margin-bottom:20px;">
            <i class="fas fa-arrow-left"></i> Retour au signalement #{{ $signalement->id }}
        </a>

        {{-- En-tête accident --}}
        <div class="notif-header">
            <i class="fas fa-car-crash bg-icon"></i>
            <div style="position:relative;z-index:2;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:4px;">
                    <span style="background:rgba(255,255,255,0.2);color:white;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:800;">SIGNALEMENT #{{ $signalement->id }}</span>
                    @if($signalement->statut === 'traite')
                        <span style="background:white;color:#16A34A;padding:3px 10px;border-radius:6px;font-size:10px;font-weight:800;"><i class="fas fa-check mr-1"></i>TRAITÉ</span>
                    @endif
                </div>
                <h1>Notification d'accident aux contacts d'urgence</h1>
                <p>
                    <i class="far fa-calendar-alt" style="margin-right:4px;"></i>
                    {{ $signalement->created_at->format('d/m/Y à H:i') }}
                    @if($signalement->programme)
                        — {{ $signalement->programme->point_depart }} <i class="fas fa-arrow-right" style="font-size:9px;margin:0 4px;"></i> {{ $signalement->programme->point_arrive }}
                    @endif
                </p>

                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;">
                    <span class="stat-badge" style="background:rgba(255,255,255,0.15);color:white;">
                        <i class="fas fa-skull-crossbones"></i> {{ $signalement->nombre_morts ?? 0 }} Mort(s)
                    </span>
                    <span class="stat-badge" style="background:rgba(255,255,255,0.15);color:white;">
                        <i class="fas fa-user-injured"></i> {{ $signalement->nombre_blesses ?? 0 }} Blessé(s)
                    </span>
                    <span class="stat-badge" style="background:rgba(37,99,235,0.8);color:white;">
                        <i class="fas fa-ambulance"></i> {{ $passagersEvacues->count() }} Évacué(s)
                    </span>
                    <span class="stat-badge" style="background:rgba(22,163,74,0.8);color:white;">
                        <i class="fas fa-heart"></i> {{ $countIndemnes }} Indemne(s)
                    </span>
                </div>
            </div>
        </div>

        @if($passagersEvacues->count() === 0)
            <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:60px 32px;text-align:center;">
                <div style="width:64px;height:64px;background:#DCFCE7;border-radius:50%;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-check" style="font-size:24px;color:#16A34A;"></i>
                </div>
                <h3 style="font-size:18px;font-weight:800;color:var(--text-1);margin-bottom:6px;">Aucun passager évacué</h3>
                <p style="font-size:13px;color:var(--text-3);font-weight:500;">Tous les passagers ont été marqués comme indemnes. Aucune notification nécessaire.</p>
            </div>
        @else

            <form action="{{ route('compagnie.signalement.envoyer-notifications', $signalement->id) }}" method="POST" id="notificationForm">
                @csrf

                {{-- Blocs par hôpital --}}
                @foreach($parHopital as $hopitalNom => $passagers)
                    <div class="hospital-block">
                        <div class="hospital-header">
                            <div class="hospital-icon"><i class="fas fa-hospital"></i></div>
                            <div style="flex:1;">
                                <div style="font-size:16px;font-weight:800;color:var(--text-1);">{{ $hopitalNom }}</div>
                                @php $adresse = $passagers->first()['hopital_adresse'] ?? ''; @endphp
                                @if($adresse)
                                    <div style="font-size:11px;color:var(--text-3);font-weight:600;margin-top:2px;">
                                        <i class="fas fa-map-marker-alt" style="color:#DC2626;margin-right:4px;"></i> {{ $adresse }}
                                    </div>
                                @endif
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="background:#FEF2F2;color:#DC2626;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:800;">
                                    {{ $passagers->count() }} évacué(s)
                                </span>
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:10px;font-weight:700;color:var(--text-3);text-transform:uppercase;">
                                    <input type="checkbox" checked class="select-all-hospital"
                                        onchange="toggleHospitalGroup(this, '{{ Str::slug($hopitalNom) }}')"
                                        style="width:16px;height:16px;accent-color:#2563EB;">
                                    Tout
                                </label>
                            </div>
                        </div>

                        @foreach($passagers as $p)
                            @php $contactUrgence = $p['contact_urgence']; @endphp
                            <div class="passenger-row">
                                {{-- Checkbox --}}
                                <div style="flex-shrink:0;">
                                    @if($contactUrgence)
                                        <input type="checkbox" name="contacts[]" value="{{ $contactUrgence }}"
                                            checked class="contact-checkbox hospital-{{ Str::slug($hopitalNom) }}"
                                            style="width:18px;height:18px;accent-color:#2563EB;cursor:pointer;">
                                    @else
                                        <input type="checkbox" disabled style="width:18px;height:18px;opacity:0.3;">
                                    @endif
                                </div>

                                {{-- Siège --}}
                                <div class="seat-badge">{{ $p['seat'] }}</div>

                                {{-- Nom --}}
                                <div style="flex:1;min-width:0;">
                                    <div style="font-weight:800;font-size:14px;color:var(--text-1);">{{ $p['nom'] }}</div>
                                    <div style="font-size:11px;color:var(--text-3);font-weight:600;">{{ $p['telephone_passager'] ?? 'Tél. non renseigné' }}</div>
                                </div>

                                {{-- Contact urgence --}}
                                <div style="flex-shrink:0;">
                                    @if($contactUrgence)
                                        <div class="ice-chip">
                                            <div class="ice-avatar"><i class="fas fa-phone-alt"></i></div>
                                            <div>
                                                <div style="font-weight:800;font-size:13px;color:#DC2626;">{{ $contactUrgence }}</div>
                                                <div style="font-size:10px;color:var(--text-3);font-weight:600;">{{ $p['nom_contact_urgence'] }}</div>
                                            </div>
                                        </div>
                                    @else
                                        <span style="font-size:11px;color:var(--text-3);font-style:italic;">Contact non renseigné</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                {{-- Section message --}}
                <div class="message-section">
                    <div class="message-section-header">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:40px;height:40px;background:white;border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(37,99,235,0.12);">
                                <i class="fas fa-envelope" style="color:#2563EB;font-size:16px;"></i>
                            </div>
                            <div>
                                <div style="font-size:15px;font-weight:800;color:#1E3A5F;">Message à envoyer aux contacts d'urgence</div>
                                <div style="font-size:11px;color:#3B82F6;font-weight:600;margin-top:2px;">Ce message sera envoyé par email aux contacts sélectionnés</div>
                            </div>
                        </div>
                    </div>

                    <div class="message-section-body">
                        {{-- Boutons d'insertion rapide --}}
                        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
                            @foreach($parHopital as $hopitalNom => $passagers)
                                <button type="button" onclick="insertHospitalInMessage('{{ $hopitalNom }}', '{{ $passagers->first()['hopital_adresse'] ?? '' }}')" class="tag-hospital">
                                    <i class="fas fa-hospital"></i> Insérer : {{ $hopitalNom }}
                                </button>
                            @endforeach
                        </div>

                        <textarea name="message" id="notification-message" class="custom-textarea" required>Bonjour,

Nous vous informons qu'un accident de la route s'est produit le {{ $signalement->created_at->format('d/m/Y à H:i') }} sur le trajet {{ $signalement->programme->point_depart ?? '' }} → {{ $signalement->programme->point_arrive ?? '' }} ({{ $compagnie->name }}).

Votre proche a été pris en charge et évacué vers l'hôpital suivant :
@foreach($parHopital as $hopitalNom => $passagers)
- {{ $hopitalNom }}{{ $passagers->first()['hopital_adresse'] ? ' — ' . $passagers->first()['hopital_adresse'] : '' }}
@endforeach

Nous vous invitons à vous rapprocher de l'établissement pour plus d'informations sur l'état de votre proche.

Cordialement,
{{ $compagnie->name }}</textarea>

                        <div class="count-bar">
                            <div style="font-size:12px;color:var(--text-3);font-weight:700;">
                                <i class="fas fa-check-circle" style="color:#2563EB;margin-right:4px;"></i>
                                <span id="selected-count">{{ $passagersEvacues->where('contact_urgence', '!=', null)->count() }}</span> contact(s) sélectionné(s)
                            </div>
                            <button type="submit" class="btn-send">
                                <i class="fas fa-paper-plane"></i> Envoyer les notifications
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
function toggleHospitalGroup(masterCheckbox, hospitalSlug) {
    document.querySelectorAll(`.hospital-${hospitalSlug}`).forEach(cb => {
        cb.checked = masterCheckbox.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.contact-checkbox:checked').length;
    const el = document.getElementById('selected-count');
    if (el) el.textContent = checked;
}

function insertHospitalInMessage(nom, adresse) {
    const textarea = document.getElementById('notification-message');
    const info = nom + (adresse ? ' — ' + adresse : '');
    const pos = textarea.selectionStart;
    textarea.value = textarea.value.substring(0, pos) + info + textarea.value.substring(pos);
    textarea.focus();
    textarea.selectionStart = textarea.selectionEnd = pos + info.length;
}

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('contact-checkbox')) updateSelectedCount();
});
</script>
@endsection
