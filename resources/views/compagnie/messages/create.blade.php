@extends('compagnie.layouts.template')

@section('styles')
<style>
    :root {
        --primary-accent: #e94f1b;
        --secondary-bg: #f8fafc;
    }

    .form-glass-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 32px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 3rem 2rem;
        text-align: center;
    }

    .input-field {
        background: var(--secondary-bg);
        border: 2px solid transparent;
        border-radius: 16px;
        padding: 1rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .input-field:focus {
        background: white;
        border-color: var(--primary-accent);
        box-shadow: 0 0 0 4px rgba(233, 79, 27, 0.1);
        outline: none;
    }

    .custom-label {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.75rem;
        display: block;
    }

    .btn-send {
        background: var(--primary-accent);
        color: white;
        padding: 1.25rem;
        border-radius: 20px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        box-shadow: 0 10px 20px -5px rgba(233, 79, 27, 0.3);
        transition: all 0.3s ease;
    }

    .btn-send:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px -5px rgba(233, 79, 27, 0.4);
        color: white;
    }

    .back-link {
        color: #94a3b8;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        color: var(--primary-accent);
        text-decoration: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <a href="{{ route('compagnie.messages.index') }}" class="back-link inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour au centre de messages
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="form-glass-card">
                <div class="form-header">
                    <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl shadow-orange-900/20">
                        <i class="fas fa-paper-plane text-white text-2xl"></i>
                    </div>
                    <h2 class="text-white text-3xl font-black">Nouveau Message</h2>
                    <p class="text-slate-400 mt-2 font-medium">Diffusez une information importante à vos équipes</p>
                </div>

                <div class="p-8 md:p-12">
                    <form action="{{ route('compagnie.messages.store') }}" method="POST" id="messageForm">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="custom-label">Cible de communication</label>
                                <select class="input-field w-full" id="recipient_type" name="recipient_type" required>
                                    <option value="" selected disabled>Choisir un profil...</option>
                                    <option value="agent">👨‍💼 Agents</option>
                                    <option value="caisse">💰 Caisse</option>
                                    <option value="personnel">🚛 Chauffeurs / Personnel</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="custom-label">Destinataire spécifique</label>
                                <select class="input-field w-full disabled:opacity-50" id="recipient_id" name="recipient_id" required disabled>
                                    <option value="" selected disabled>Sélectionner &larr;</option>
                                </select>
                                <div id="loading-recipients" class="hidden mt-2 text-orange-500 text-xs font-bold uppercase tracking-widest">
                                    <i class="fas fa-sync fa-spin mr-1"></i> Synchronisation...
                                </div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="custom-label">Objet de la communication</label>
                            <input type="text" class="input-field w-full" id="subject" name="subject" required placeholder="Saisissez le titre du message...">
                        </div>

                        <div class="mb-10">
                            <label class="custom-label">Contenu du message</label>
                            <textarea class="input-field w-full min-h-[200px] resize-none" id="message" name="message" rows="6" required placeholder="Rédigez ici votre communication professionnelle..."></textarea>
                        </div>

                        <button type="submit" class="btn-send w-full flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-3 text-lg"></i>
                            Envoyer maintenant
                        </button>
                    </form>
                </div>
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
            loadingIndicator.classList.remove('hidden');

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
                        let label = `${recipient.name} ${recipient.prenom}`;
                        if (recipient.type_personnel) {
                            label += ` [${recipient.type_personnel}]`;
                        }
                        option.textContent = label;
                        recipientSelect.appendChild(option);
                    });
                    
                    recipientSelect.disabled = false;
                    loadingIndicator.classList.add('hidden');
                })
                .catch(error => {
                    recipientSelect.innerHTML = '<option disabled>Erreur réseau</option>';
                    loadingIndicator.classList.add('hidden');
                });
        });
    });
</script>
@endsection
