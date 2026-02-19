@extends('gare-espace.layouts.template')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="msg-compose-wrapper">
    <div class="mb-4">
        <a href="{{ route('gare-espace.messages.index') }}" class="back-link">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la boîte de réception
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-glass-card animate__animated animate__fadeInUp">
                <div class="form-header">
                    <div class="header-icon">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <h2 class="header-title">Nouveau Message</h2>
                    <p class="header-sub">Envoyez un message à vos équipes ou à la compagnie</p>
                </div>

                <div class="form-body">
                    <form action="{{ route('gare-espace.messages.store') }}" method="POST" id="messageForm">
                        @csrf

                        <div class="form-row">
                            <div class="form-col">
                                <label class="custom-label">Cible de communication</label>
                                <select class="input-field" id="recipient_type" name="recipient_type" required>
                                    <option value="" selected disabled>Choisir un profil...</option>
                                    <option value="agent">👨‍💼 Agents</option>
                                    <option value="caisse">💰 Caisse</option>
                                    <option value="personnel">🚛 Chauffeurs / Personnel</option>
                                    <option value="compagnie">🏢 Compagnie</option>
                                </select>
                            </div>

                            <div class="form-col">
                                <label class="custom-label">Destinataire spécifique</label>
                                <select class="input-field" id="recipient_id" name="recipient_id" required disabled>
                                    <option value="" selected disabled>Sélectionner ←</option>
                                </select>
                                <div id="loading-recipients" class="loading-indicator" style="display: none;">
                                    <i class="fas fa-sync fa-spin"></i> Synchronisation...
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="custom-label">Objet de la communication</label>
                            <input type="text" class="input-field" id="subject" name="subject" required placeholder="Saisissez le titre du message...">
                        </div>

                        <div class="form-group">
                            <label class="custom-label">Contenu du message</label>
                            <textarea class="input-field textarea" id="message" name="message" rows="6" required placeholder="Rédigez ici votre communication professionnelle..."></textarea>
                        </div>

                        <button type="submit" class="btn-send">
                            <i class="fas fa-paper-plane"></i>
                            Envoyer maintenant
                        </button>
                    </form>
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

.msg-compose-wrapper {
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

.form-glass-card {
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 2rem;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08);
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: 3rem 2rem;
    text-align: center;
}

.header-icon {
    width: 64px;
    height: 64px;
    background: var(--primary);
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    box-shadow: 0 10px 20px rgba(233, 79, 27, 0.3);
}

.header-icon i {
    color: white;
    font-size: 1.5rem;
}

.header-title {
    color: white;
    font-size: 1.75rem;
    font-weight: 800;
    margin: 0;
}

.header-sub {
    color: #94a3b8;
    margin: 0.5rem 0 0;
    font-weight: 500;
}

.form-body {
    padding: 2.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.custom-label {
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 0.75rem;
    display: block;
}

.input-field {
    width: 100%;
    background: #f8fafc;
    border: 2px solid transparent;
    border-radius: 1rem;
    padding: 1rem 1.25rem;
    font-weight: 500;
    font-family: var(--font-family);
    color: var(--text-main);
    transition: all 0.3s ease;
}

.input-field:focus {
    background: white;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(233, 79, 27, 0.1);
    outline: none;
}

.input-field:disabled {
    opacity: 0.5;
}

.textarea {
    min-height: 160px;
    resize: none;
}

.loading-indicator {
    margin-top: 0.5rem;
    color: var(--primary);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.btn-send {
    width: 100%;
    background: var(--primary);
    color: white;
    padding: 1.25rem;
    border-radius: 1.25rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    border: none;
    cursor: pointer;
    font-family: var(--font-family);
    font-size: 0.9rem;
    box-shadow: 0 10px 20px -5px rgba(233, 79, 27, 0.3);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}

.btn-send:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 25px -5px rgba(233, 79, 27, 0.4);
}

@media (max-width: 768px) {
    .msg-compose-wrapper { padding: 1rem; }
    .form-row { grid-template-columns: 1fr; }
    .form-body { padding: 1.5rem; }
    .form-header { padding: 2rem 1.5rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('recipient_type');
    const recipientSelect = document.getElementById('recipient_id');
    const loadingIndicator = document.getElementById('loading-recipients');

    typeSelect.addEventListener('change', function() {
        const type = this.value;

        recipientSelect.innerHTML = '<option value="" selected disabled>Chargement...</option>';
        recipientSelect.disabled = true;
        loadingIndicator.style.display = 'block';

        fetch(`{{ route('gare-espace.messages.recipients') }}?type=${type}`)
            .then(response => response.json())
            .then(data => {
                recipientSelect.innerHTML = '<option value="" selected disabled>Sélectionner le destinataire...</option>';

                if(data.length === 0) {
                    recipientSelect.innerHTML = '<option disabled>Aucun destinataire trouvé</option>';
                }

                data.forEach(recipient => {
                    const option = document.createElement('option');
                    option.value = recipient.id;
                    let label = `${recipient.name} ${recipient.prenom || ''}`.trim();
                    if (recipient.type_personnel) {
                        label += ` [${recipient.type_personnel}]`;
                    }
                    option.textContent = label;
                    recipientSelect.appendChild(option);
                });

                recipientSelect.disabled = false;
                loadingIndicator.style.display = 'none';

                // Auto-select compagnie (only one recipient)
                if (type === 'compagnie' && data.length === 1) {
                    recipientSelect.value = data[0].id;
                    recipientSelect.disabled = true;
                }
            })
            .catch(error => {
                recipientSelect.innerHTML = '<option disabled>Erreur réseau</option>';
                loadingIndicator.style.display = 'none';
            });
    });

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
});
</script>
@endsection
