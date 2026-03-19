@extends('compagnie.layouts.template')

@section('styles')
<style>
    .form-wrapper { max-width: 800px; margin: 0 auto; }
    
    .btn-back { display: inline-flex; align-items: center; gap: 8px; color: var(--text-3); font-weight: 700; font-size: 13px; text-decoration: none; margin-bottom: 24px; transition: color 0.2s; }
    .btn-back:hover { color: var(--orange); text-decoration: none; }

    .form-glass-card { background: var(--surface); border: 1px solid var(--border); border-radius: 24px; overflow: hidden; box-shadow: var(--shadow-md); }
    
    .form-header { background: linear-gradient(135deg, #1A1714 0%, #2A2520 100%); padding: 40px 20px; text-align: center; }
    .form-header-icon { width: 64px; height: 64px; background: var(--orange); border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; margin: 0 auto 16px; box-shadow: 0 8px 24px rgba(249,115,22,0.3); }
    .form-header-title { font-size: 24px; font-weight: 800; color: white; margin-bottom: 4px; }
    .form-header-subtitle { font-size: 13px; color: rgba(255,255,255,0.7); }

    .form-body { padding: 40px; }
    .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
    .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
    
    .custom-label { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--text-3); letter-spacing: 0.5px; margin: 0; }
    .input-field { width: 100%; background: var(--surface-2); border: 2px solid transparent; border-radius: 14px; padding: 14px 18px; font-size: 13px; font-weight: 600; color: var(--text-1); transition: all 0.2s; outline: none; }
    .input-field:focus { background: var(--surface); border-color: var(--orange); box-shadow: 0 0 0 4px var(--orange-light); }
    .input-field:disabled { opacity: 0.6; cursor: not-allowed; }
    
    textarea.input-field { min-height: 180px; resize: vertical; line-height: 1.6; }

    .btn-send { background: var(--orange); color: white; padding: 16px; border-radius: 14px; font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; border: none; width: 100%; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: pointer; transition: all 0.2s; box-shadow: 0 8px 20px rgba(249,115,22,0.25); }
    .btn-send:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(249,115,22,0.35); }

    .loading-text { font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--orange); margin-top: 6px; display: none; }
    .loading-text.visible { display: block; }

    @media (max-width: 640px) {
        .form-grid-2 { grid-template-columns: 1fr; gap: 16px; }
        .form-body { padding: 24px; }
    }
</style>
@endsection

@section('content')
<div class="dashboard-page">
    <div class="form-wrapper">
        <a href="{{ route('compagnie.messages.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Retour au centre de messages
        </a>

        <div class="form-glass-card">
            <div class="form-header">
                <div class="form-header-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <h2 class="form-header-title">Nouveau Message</h2>
                <p class="form-header-subtitle">Diffusez une information importante à vos équipes</p>
            </div>

            <div class="form-body">
                <form action="{{ route('compagnie.messages.store') }}" method="POST" id="messageForm">
                    @csrf
                    
                    <div class="form-grid-2">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="custom-label">Cible de communication</label>
                            <select class="input-field" id="recipient_type" name="recipient_type" required>
                                <option value="" selected disabled>Choisir un profil...</option>
                                <option value="agent">👨‍💼 Agents</option>
                                <option value="caisse">💰 Caisse</option>
                                <option value="gare">⛪ Gares</option>
                                <option value="personnel">🚛 Chauffeurs / Personnel</option>
                            </select>
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="custom-label">Destinataire spécifique</label>
                            <select class="input-field" id="recipient_id" name="recipient_id" required disabled>
                                <option value="" selected disabled>Sélectionner &larr;</option>
                            </select>
                            <div id="loading-recipients" class="loading-text">
                                <i class="fas fa-sync fa-spin mr-1"></i> Synchronisation...
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="custom-label">Objet de la communication</label>
                        <input type="text" class="input-field" id="subject" name="subject" required placeholder="Saisissez le titre du message...">
                    </div>

                    <div class="form-group">
                        <label class="custom-label">Contenu du message</label>
                        <textarea class="input-field" id="message" name="message" required placeholder="Rédigez ici votre communication professionnelle..."></textarea>
                    </div>

                    <button type="submit" class="btn-send">
                        <i class="fas fa-paper-plane"></i> Envoyer maintenant
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('recipient_type');
        const recipientSelect = document.getElementById('recipient_id');
        const loadingIndicator = document.getElementById('loading-recipients');

        typeSelect.addEventListener('change', function() {
            const type = this.value;
            
            recipientSelect.innerHTML = '<option value="" selected disabled>Chargement...</option>';
            recipientSelect.disabled = true;
            loadingIndicator.classList.add('visible');

            fetch(`{{ route('compagnie.messages.recipients') }}?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    recipientSelect.innerHTML = '<option value="" selected disabled>Sélectionner le destinataire...</option>';
                    
                    if(data.length === 0) {
                        recipientSelect.innerHTML = '<option disabled>Aucun destinataire trouvé</option>';
                    }

                    data.forEach(recipient => {
                        const option = document.createElement('option');
                        option.value = recipient.id;
                        let label = recipient.name;
                        if (recipient.prenom) {
                            label += ` ${recipient.prenom}`;
                        }
                        if (recipient.type_personnel) {
                            label += ` [${recipient.type_personnel}]`;
                        }
                        option.textContent = label;
                        recipientSelect.appendChild(option);
                    });
                    
                    recipientSelect.disabled = false;
                    loadingIndicator.classList.remove('visible');
                })
                .catch(error => {
                    recipientSelect.innerHTML = '<option disabled>Erreur réseau</option>';
                    loadingIndicator.classList.remove('visible');
                });
        });
    });
</script>
@endsection