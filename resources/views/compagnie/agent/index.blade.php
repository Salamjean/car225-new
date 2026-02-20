@extends('compagnie.layouts.template')

@section('content')
<!-- Import Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="content-wrapper-modern">
    <!-- Top Stats Bar -->
    <div class="stats-overview animate__animated animate__fadeInDown">
        <div class="stat-item">
            <div class="stat-icon-box bg-gradient-blue shadow-blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Total Agents</span>
                <h3 class="stat-number">{{ $totalAgents }}</h3>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-box bg-gradient-green shadow-green">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Actifs</span>
                <h3 class="stat-number">{{ $activeAgents }}</h3>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon-box bg-gradient-orange shadow-orange">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Nouveaux (7j)</span>
                <h3 class="stat-number">{{ $newAgents }}</h3>
            </div>
        </div>
        <div class="stat-item d-none d-xl-flex">
            <div class="stat-icon-box bg-gradient-purple shadow-purple">
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Communes</span>
                <h3 class="stat-number">{{ $agents->pluck('commune')->filter()->unique()->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Header Actions -->
    <div class="action-header animate__animated animate__fadeIn">
        <div class="header-left">
            <h1 class="main-title">Gestion de l'équipe</h1>
            <p class="main-subtitle">Contrôlez les accès et suivez les performances de vos agents</p>
        </div>
        <div class="header-right">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" id="agentSearch" placeholder="Rechercher un agent, une commune...">
            </div>
            <button class="btn btn-filter" id="toggleFilters">
                <i class="fas fa-sliders-h"></i>
                <span>Filtres</span>
            </button>
            <a href="{{ route('compagnie.agents.create') }}" class="btn btn-primary-modern">
                <i class="fas fa-plus-circle"></i>
                <span>Nouvel Agent</span>
            </a>
        </div>
    </div>

    <!-- Main Content Table -->
    <div class="main-card-modern animate__animated animate__fadeInUp">
        <div class="table-container">
            @if($agents->count() > 0)
            <table class="premium-table">
                <thead>
                    <tr>
                        <th class="col-agent">AGENT</th>
                        <th class="col-contact">COORDONNÉES</th>
                        <th class="col-gare">AFFECTATION</th>
                        <th class="col-status">STATUT</th>
                        <th class="col-actions text-end">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agents as $index => $agent)
                    <tr style="--delay: {{ $index * 0.05 }}s" class="agent-row">
                        <td>
                            <div class="agent-profile">
                                <div class="avatar-container">
                                    @if($agent->profile_picture)
                                        <img src="{{ Storage::url($agent->profile_picture) }}" alt="{{ $agent->name }}">
                                    @else
                                        <div class="avatar-initials">
                                            {{ substr($agent->name, 0, 1) }}{{ substr($agent->prenom, 0, 1) }}
                                        </div>
                                    @endif
                                    <div class="status-indicator {{ $agent->is_active ? 'active' : 'inactive' }}"></div>
                                </div>
                                <div class="profile-meta">
                                    <span class="name">{{ $agent->name }} {{ $agent->prenom }}</span>
                                    <span class="id">#{{ str_pad($agent->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-stack">
                                <a href="tel:{{ $agent->contact }}" class="contact-line">
                                    <i class="fas fa-phone-alt"></i>
                                    {{ $agent->contact }}
                                </a>
                                <a href="mailto:{{ $agent->email }}" class="contact-line email">
                                    <i class="fas fa-envelope"></i>
                                    {{ $agent->email }}
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="assignment-box">
                                <span class="commune"><i class="fas fa-map-marker-alt"></i> {{ $agent->commune ?? 'Non défini' }}</span>
                                @if($agent->gare)
                                    <span class="gare-badge"><i class="fas fa-building"></i> {{ $agent->gare->nom_gare }}</span>
                                @else
                                    <span class="no-gare">Non affecté</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($agent->is_active)
                                <span class="badge-status success">
                                    <i class="fas fa-check-circle"></i> Actif
                                </span>
                            @else
                                <span class="badge-status danger">
                                    <i class="fas fa-times-circle"></i> Inactif
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons justify-content-end">
                                <button class="btn-icon-modern edit" title="Modifier" onclick="window.location.href='{{ route('compagnie.agents.edit', $agent->id) }}'">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn-icon-modern message" title="Message" data-bs-toggle="modal" data-bs-target="#messageModal" 
                                        data-agent-id="{{ $agent->id }}" data-agent-name="{{ $agent->name }} {{ $agent->prenom }}">
                                    <i class="fas fa-comment-dots"></i>
                                </button>
                                <button class="btn-icon-modern delete" title="Supprimer" onclick="confirmDeleteAgent({{ $agent->id }})">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="pagination-footer">
                <div class="pagination-info">
                    Affichage de <strong>{{ $agents->firstItem() }}</strong> à <strong>{{ $agents->lastItem() }}</strong> sur <strong>{{ $agents->total() }}</strong>
                </div>
                <div class="pagination-links">
                    {{ $agents->links() }}
                </div>
            </div>
            @else
            <div class="empty-dashboard">
                <div class="empty-animation">
                    <i class="fas fa-user-astronaut"></i>
                </div>
                <h3>L'équipe est encore vide</h3>
                <p>Commencez à construire votre réseau d'agents pour gérer vos gares en quelques clics.</p>
                <a href="{{ route('compagnie.agents.create') }}" class="btn btn-primary-modern">
                    Ajouter le premier membre
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-modal">
            <form action="{{ route('compagnie.agents.send-message') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="icon-circle"><i class="fas fa-paper-plane"></i></span>
                        Contacter <span id="modalAgentName" class="highlight"></span>
                    </h5>
                    <button type="button" class="btn-close-glass" data-bs-dismiss="modal"><i class="fas fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="agent_id" id="modalAgentId">
                    <div class="form-group-modern mb-3">
                        <label>Sujet du message</label>
                        <input type="text" name="subject" required placeholder="Ex: Modification de planning">
                    </div>
                    <div class="form-group-modern">
                        <label>Votre message</label>
                        <textarea name="message" rows="4" required placeholder="Écrivez ici..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-link-modern" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary-modern">Envoyer le message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form (Hidden) -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
/* Modern CSS Reset & Variables */
:root {
    --primary: #e94f1b;
    --primary-light: #ff6b3d;
    --primary-dark: #c13e13;
    --secondary: #10b981;
    --dark: #121415;
    --gray-bg: #f5f7fb;
    --card-bg: #ffffff;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --font-family: 'Plus Jakarta Sans', sans-serif;
}

body {
    background-color: var(--gray-bg);
}

.content-wrapper-modern {
    padding: 2rem;
    font-family: var(--font-family);
    max-width: 1600px;
    margin: 0 auto;
}

/* Stats Overview */
.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.stat-item {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    border: 1px solid var(--border-color);
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

.stat-icon-box {
    width: 56px;
    height: 56px;
    border-radius: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }
.bg-gradient-orange { background: linear-gradient(135deg, #f97316, #ea580c); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }

.stat-info .stat-label {
    font-size: 0.875rem;
    color: var(--text-muted);
    font-weight: 500;
}

.stat-info .stat-number {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-main);
    margin: 0;
}

/* Header & Search */
.action-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.main-title {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.02em;
    margin-bottom: 0.25rem;
}

.main-subtitle {
    color: var(--text-muted);
    margin: 0;
}

.header-right {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.search-wrapper {
    position: relative;
    min-width: 300px;
}

.search-wrapper i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.search-wrapper input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.75rem;
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    background: white;
    font-weight: 500;
    transition: all 0.2s;
}

.search-wrapper input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(233, 79, 27, 0.1);
    outline: none;
}

/* Buttons */
.btn-primary-modern {
    background: var(--primary);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 700;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.2s;
    box-shadow: 0 4px 6px rgba(233, 79, 27, 0.2);
}

.btn-primary-modern:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    color: white;
    box-shadow: 0 6px 12px rgba(233, 79, 27, 0.3);
}

.btn-filter {
    background: white;
    border: 1px solid var(--border-color);
    padding: 0.75rem 1.25rem;
    border-radius: 0.75rem;
    font-weight: 600;
    color: var(--text-main);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Card & Table */
.main-card-modern {
    background: white;
    border-radius: 1.5rem;
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.premium-table {
    width: 100%;
    border-collapse: collapse;
}

.premium-table thead th {
    background: #f8fafc;
    padding: 1.25rem 2rem;
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-muted);
    letter-spacing: 0.05em;
    border-bottom: 2px solid #f1f5f9;
}

.agent-row {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.2s;
    opacity: 0;
    animation: slideIn 0.5s ease forwards var(--delay);
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}

.agent-row:hover {
    background: #fcfdfe;
}

.agent-row td {
    padding: 1.25rem 2rem;
    vertical-align: middle;
}

/* Agent Profile */
.agent-profile {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}

.avatar-container {
    position: relative;
    width: 52px;
    height: 52px;
}

.avatar-container img {
    width: 100%;
    height: 100%;
    border-radius: 1rem;
    object-fit: cover;
    background: #f1f5f9;
}

.avatar-initials {
    width: 100%;
    height: 100%;
    border-radius: 1rem;
    background: linear-gradient(135deg, #e94f1b, #f97316);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.1rem;
}

.status-indicator {
    position: absolute;
    bottom: -3px;
    right: -3px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
}

.status-indicator.active { background: #10b981; }
.status-indicator.inactive { background: #ef4444; }

.profile-meta .name {
    display: block;
    font-weight: 700;
    color: var(--text-main);
    font-size: 1.05rem;
    line-height: 1.2;
}

.profile-meta .id {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
}

/* Contacts */
.contact-stack {
    display: flex;
    flex-direction: column;
    gap: 0.4rem;
}

.contact-line {
    text-decoration: none !important;
    color: var(--text-main);
    font-size: 0.9rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    transition: color 0.2s;
}

.contact-line i { color: var(--primary); font-size: 0.85rem; }
.contact-line.email i { color: #3b82f6; }
.contact-line:hover { color: var(--primary); }

/* Assignment */
.assignment-box {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.commune {
    font-size: 0.9rem;
    font-weight: 700;
    color: var(--text-main);
}

.commune i { color: #3b82f6; margin-right: 4px; }

.gare-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    background: #eff6ff;
    color: #1d4ed8;
    border-radius: 0.5rem;
    font-size: 0.8rem;
    font-weight: 700;
    width: fit-content;
}

.no-gare {
    font-size: 0.8rem;
    font-style: italic;
    color: var(--text-muted);
}

/* Badges */
.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.85rem;
    font-weight: 700;
}

.badge-status.success { background: #ecfdf5; color: #065f46; }
.badge-status.danger { background: #fef2f2; color: #991b1b; }

/* Actions */
.btn-icon-modern {
    width: 38px;
    height: 38px;
    border-radius: 0.75rem;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-left: 0.5rem;
    transition: all 0.2s;
    font-size: 0.9rem;
}

.btn-icon-modern.edit { background: #f1f5f9; color: #475569; }
.btn-icon-modern.message { background: #eff6ff; color: #1d4ed8; }
.btn-icon-modern.delete { background: #fef2f2; color: #ef4444; }

.btn-icon-modern:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

.btn-icon-modern.edit:hover { background: #e2e8f0; }
.btn-icon-modern.message:hover { background: #dbeafe; }
.btn-icon-modern.delete:hover { background: #fee2e2; }

/* Pagination Footer */
.pagination-footer {
    padding: 1.5rem 2rem;
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Modals */
.glass-modal {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.5);
}

.modal-header {
    border-bottom: none;
    padding: 1.5rem 2rem;
}

.inner-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-light);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.form-group-modern label {
    display: block;
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    color: var(--text-main);
}

.form-group-modern input, .form-group-modern textarea {
    width: 100%;
    padding: 0.8rem 1rem;
    border-radius: 0.75rem;
    border: 1px solid var(--border-color);
    background: #f8fafc;
    font-weight: 500;
}

.btn-link-modern {
    background: none;
    border: none;
    font-weight: 700;
    color: var(--text-muted);
}

/* Empty State */
.empty-dashboard {
    padding: 5rem 2rem;
    text-align: center;
}

.empty-animation {
    font-size: 5rem;
    color: var(--primary);
    margin-bottom: 2rem;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .content-wrapper-modern { padding: 1rem; }
    .header-right { width: 100%; }
    .search-wrapper { flex-grow: 1; }
    .main-title { font-size: 1.5rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Agent Search Logic
    const searchInput = document.getElementById('agentSearch');
    const tableRows = document.querySelectorAll('.agent-row');

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        tableRows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    // Message Modal Setup
    const messageModal = document.getElementById('messageModal');
    if (messageModal) {
        messageModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const agentId = button.getAttribute('data-agent-id');
            const agentName = button.getAttribute('data-agent-name');
            
            document.getElementById('modalAgentId').value = agentId;
            document.getElementById('modalAgentName').innerText = agentName;
        });
    }

    // Modern Delete Confirmation
    window.confirmDeleteAgent = function(agentId) {
        Swal.fire({
            title: 'Souhaitez-vous vraiment retirer cet agent ?',
            text: "Cette action désactivera ses accès et supprimera ses données liées.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e94f1b',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler',
            customClass: {
                popup: 'premium-swal-popup',
                confirmButton: 'premium-swal-confirm'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/company/agent/${agentId}`;
                form.submit();
            }
        });
    }

    // Tost Confirmation (if exists in session)
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Opération réussie',
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