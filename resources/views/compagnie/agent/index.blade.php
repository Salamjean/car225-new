@extends('compagnie.layouts.template')
@section('content')
<div class="container-fluid">
    <!-- En-tête de page -->
    <div class="row page-header-modern">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between p-4">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <div class="header-icon-wrapper me-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h1 class="page-title mb-1">Gestion des agents</h1>
                        <p class="page-subtitle text-muted mb-0">Consultez et gérez les agents de votre compagnie</p>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap gap-3">
                    <a href="{{ route('compagnie.dashboard') }}" class="btn-back-modern">
                        <i class="fas fa-arrow-left me-2"></i>
                        Tableau de bord
                    </a>
                    <a href="{{ route('compagnie.agents.create') }}" class="btn-add-modern">
                        <i class="fas fa-user-plus me-2"></i>
                        Nouvel agent
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Carte principale -->
    <div class="modern-card">
        <!-- En-tête de la carte -->
        <div class="card-header-modern">
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                <div>
                    <h2 class="card-title mb-2">Liste des agents</h2>
                    <p class="card-subtitle mb-0">
                        <span class="badge-count-modern">{{ $agents->count() }}</span> 
                        agent{{ $agents->count() > 1 ? 's' : '' }} trouvé{{ $agents->count() > 1 ? 's' : '' }}
                    </p>
                </div>
                
                <div class="d-flex flex-wrap gap-3 mt-3 mt-md-0">
                    <!-- Filtres -->
                    <div class="filter-group-modern">
                        <button class="btn-filter-modern" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-2"></i>
                            <span class="filter-text">Filtrer</span>
                            <i class="fas fa-chevron-down ms-2"></i>
                        </button>
                        <div class="dropdown-menu-modern">
                            <div class="dropdown-header">
                                <i class="fas fa-filter me-2"></i>
                                Filtrer par statut
                            </div>
                            <div class="dropdown-body">
                                <div class="filter-option">
                                    <input type="checkbox" id="filter-active" checked>
                                    <label for="filter-active">
                                        <span class="status-badge status-active"></span>
                                        Actifs
                                    </label>
                                </div>
                                <div class="filter-option">
                                    <input type="checkbox" id="filter-inactive">
                                    <label for="filter-inactive">
                                        <span class="status-badge status-inactive"></span>
                                        Inactifs
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recherche -->
                    <div class="search-box-modern">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" class="search-input-modern" placeholder="Rechercher un agent...">
                        <button class="search-clear">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Corps de la carte -->
        <div class="card-body-modern">
            @if($agents->count() > 0)
                <!-- Tableau des agents -->
                <div class="table-responsive-modern">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th class="table-header-modern">
                                    <div class="d-flex align-items-center">
                                        <span>Agent</span>
                                        <button class="sort-btn" data-sort="name">
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="table-header-modern">
                                    <div class="d-flex align-items-center">
                                        <span>Contact</span>
                                        <button class="sort-btn" data-sort="contact">
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="table-header-modern">
                                    <div class="d-flex align-items-center">
                                        <span>Email</span>
                                        <button class="sort-btn" data-sort="email">
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="table-header-modern">
                                    <div class="d-flex align-items-center">
                                        <span>Localisation</span>
                                        <button class="sort-btn" data-sort="commune">
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="table-header-modern">
                                    <div class="d-flex align-items-center">
                                        <span>Statut</span>
                                        <button class="sort-btn" data-sort="status">
                                            <i class="fas fa-sort"></i>
                                        </button>
                                    </div>
                                </th>
                                <th class="table-header-modern text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $agent)
                            <tr class="table-row-modern">
                                <td>
                                    <div class="agent-info">
                                        <div class="agent-avatar">
                                            @if($agent->profile_picture)
                                                <img src="{{ Storage::url($agent->profile_picture) }}" 
                                                     alt="{{ $agent->name }}" 
                                                     class="avatar-img">
                                            @else
                                                <div class="avatar-placeholder">
                                                    {{ substr($agent->name, 0, 1) }}{{ substr($agent->prenom, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="agent-details">
                                            <h6 class="agent-name">{{ $agent->name }} {{ $agent->prenom }}</h6>
                                            <p class="agent-id">
                                                <i class="fas fa-id-card me-1"></i>
                                                ID: {{ $agent->id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <div class="contact-item">
                                            <i class="fas fa-phone me-2"></i>
                                            <a href="tel:{{ $agent->contact }}" class="contact-link">
                                                {{ $agent->contact }}
                                            </a>
                                        </div>
                                        @if($agent->cas_urgence)
                                        <div class="contact-item mt-2">
                                            <i class="fas fa-phone-alt me-2 text-danger"></i>
                                            <span class="contact-urgent">{{ $agent->cas_urgence }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="email-info">
                                        <i class="fas fa-envelope me-2"></i>
                                        <a href="mailto:{{ $agent->email }}" class="email-link">
                                            {{ $agent->email }}
                                        </a>
                                    </div>
                                </td>
                                <td>
                                    @if($agent->commune)
                                    <div class="location-info">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        <span class="location-text">{{ $agent->commune }}</span>
                                    </div>
                                    @else
                                    <span class="text-muted">Non spécifié</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="status-modern">
                                        @if($agent->is_active)
                                        <span class="status-badge status-active">
                                            <i class="fas fa-circle me-1"></i>
                                            Actif
                                        </span>
                                        @else
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-circle me-1"></i>
                                            Inactif
                                        </span>
                                        @endif
                                        <p class="status-date mt-1 mb-0">
                                            <small>Créé le {{ $agent->created_at->format('d/m/Y') }}</small>
                                        </p>
                                    </div>
                                </td>
                                <td>
                                    <div class="actions-modern">
                                        <div class="dropdown-actions-modern">
                                            <button class="btn-actions-modern" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu-modern">
                                                <a href="#" class="dropdown-item-modern">
                                                    <i class="fas fa-edit me-2"></i>
                                                    Modifier
                                                </a>
                                                <a href="#" class="dropdown-item-modern">
                                                    <i class="fas fa-eye me-2"></i>
                                                    Voir détails
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <button type="button" class="dropdown-item-modern text-danger" onclick="confirmDelete({{ $agent->id }})">
                                                    <i class="fas fa-trash-alt me-2"></i>
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Boutons d'action visibles -->
                                        <button type="button" class="btn-action-modern btn-send-email" 
                                                data-email="{{ $agent->email }}"
                                                title="Envoyer un email">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                        <button type="button" class="btn-action-modern btn-call" 
                                                data-phone="{{ $agent->contact }}"
                                                title="Appeler">
                                            <i class="fas fa-phone"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($agents->hasPages())
                <div class="pagination-modern">
                    <div class="pagination-info">
                        Affichage de {{ $agents->firstItem() }} à {{ $agents->lastItem() }} sur {{ $agents->total() }} agents
                    </div>
                    <div class="pagination-links">
                        {{ $agents->links('vendor.pagination.modern') }}
                    </div>
                </div>
                @endif

            @else
                <!-- État vide -->
                <div class="empty-state-modern">
                    <div class="empty-icon">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h3 class="empty-title">Aucun agent trouvé</h3>
                    <p class="empty-text">
                        Vous n'avez pas encore d'agents dans votre compagnie.<br>
                        Commencez par ajouter votre premier agent.
                    </p>
                    <a href="{{ route('compagnie.agents.create') }}" class="btn-add-modern">
                        <i class="fas fa-user-plus me-2"></i>
                        Ajouter un agent
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Carte statistiques -->
    <div class="row mt-4">
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="stat-card-modern">
                <div class="stat-icon stat-total">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $agents->count() }}</h3>
                    <p class="stat-label">Agents au total</p>
                </div>
            </div>
        </div>
                                             
        
        <div class="col-md-6 col-lg-4  mb-4">
            <div class="stat-card-modern">
                <div class="stat-icon stat-recent">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $agents->where('created_at', '>=', now()->subDays(7))->count() }}</h3>
                    <p class="stat-label">Ajoutés cette semaine</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="stat-card-modern">
                <div class="stat-icon stat-locations">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-value">{{ $agents->pluck('commune')->filter()->unique()->count() }}</h3>
                    <p class="stat-label">Communes différentes</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modern-modal">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close-modal" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet agent ? Cette action est irréversible.</p>
                <div class="alert alert-warning border-warning border-start border-3">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Toutes les données associées à cet agent seront également supprimées.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>
                    Annuler
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-modal-danger">
                        <i class="fas fa-trash-alt me-2"></i>
                        Supprimer définitivement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Inclure SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* Variables de design modernes */
:root {
    /* Palette principale */
    --primary-orange: #e94f1b;
    --primary-orange-light: rgba(254, 162, 25, 0.1);
    --primary-orange-dark: #e94f1b;
    --secondary-green: #10b981;
    --secondary-green-light: rgba(16, 185, 129, 0.1);
    --accent-green: #0a8c5f;
    
    /* Couleurs neutres */
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    
    /* Couleurs fonctionnelles */
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
    --info: #3b82f6;
    
    /* Effets */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-base: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    
    /* Bordures */
    --radius-sm: 0.375rem;
    --radius-base: 0.5rem;
    --radius-md: 0.75rem;
    --radius-lg: 1rem;
    --radius-xl: 1.5rem;
    
    /* Transitions */
    --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-base: 250ms cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: 350ms cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Espacements */
    --space-xs: 0.25rem;
    --space-sm: 0.5rem;
    --space-base: 1rem;
    --space-md: 1.5rem;
    --space-lg: 2rem;
    --space-xl: 3rem;
}

/* Reset et styles de base */
.container-fluid {
    padding: 0;
    max-width: 100%;
}

/* Page Header Moderne */
.page-header-modern {
    background: var(--white);
    border-bottom: 1px solid var(--gray-200);
    padding: var(--space-lg) var(--space-xl);
    margin-bottom: var(--space-xl);
}

.header-icon-wrapper {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1.75rem;
    box-shadow: var(--shadow-lg);
    transition: transform var(--transition-base);
}

.header-icon-wrapper:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-xl);
}

.page-title {
    color: var(--gray-900);
    font-weight: 800;
    font-size: 2.25rem;
    line-height: 1.1;
    margin-bottom: var(--space-xs);
    background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-700) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-subtitle {
    color: var(--gray-500);
    font-size: 1.125rem;
    font-weight: 400;
}

/* Boutons modernes */
.btn-back-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-base);
    color: var(--gray-700);
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-base);
    gap: var(--space-sm);
}

.btn-back-modern:hover {
    border-color: var(--primary-orange);
    color: var(--primary-orange);
    background: var(--primary-orange-light);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-add-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.875rem 1.75rem;
    background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
    border: none;
    border-radius: var(--radius-base);
    color: var(--white);
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-base);
    gap: var(--space-sm);
    box-shadow: var(--shadow-md);
}

.btn-add-modern:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-lg);
    background: linear-gradient(135deg, var(--primary-orange-dark) 0%, #d68909 100%);
}

/* Carte principale */
.modern-card {
    background: var(--white);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    margin-bottom: var(--space-xl);
    transition: box-shadow var(--transition-base);
}

.modern-card:hover {
    box-shadow: var(--shadow-xl);
}

.card-header-modern {
    padding: var(--space-lg) var(--space-xl);
    border-bottom: 1px solid var(--gray-200);
    background: linear-gradient(90deg, var(--gray-50) 0%, var(--white) 100%);
}

.card-title {
    color: var(--gray-900);
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: var(--space-xs);
}

.card-subtitle {
    color: var(--gray-500);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.badge-count-modern {
    background: var(--secondary-green-light);
    color: var(--secondary-green);
    padding: 0.375rem 0.875rem;
    border-radius: var(--radius-xl);
    font-weight: 600;
    font-size: 0.875rem;
    border: 1px solid var(--secondary-green);
}

/* Barre de recherche moderne */
.search-box-modern {
    position: relative;
    min-width: 280px;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    z-index: 2;
    transition: color var(--transition-fast);
}

.search-input-modern {
    width: 100%;
    padding: 0.875rem 1rem 0.875rem 3rem;
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-base);
    color: var(--gray-900);
    font-size: 0.95rem;
    transition: all var(--transition-base);
}

.search-input-modern:focus {
    outline: none;
    border-color: var(--primary-orange);
    box-shadow: 0 0 0 3px rgba(254, 162, 25, 0.15);
    background: var(--white);
}

.search-input-modern:focus + .search-icon {
    color: var(--primary-orange);
}

.search-clear {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: var(--gray-100);
    border: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    color: var(--gray-500);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all var(--transition-fast);
}

.search-input-modern:not(:placeholder-shown) ~ .search-clear {
    opacity: 1;
}

.search-clear:hover {
    background: var(--gray-200);
    color: var(--gray-700);
}

/* Boutons de filtre */
.filter-group-modern {
    position: relative;
}

.btn-filter-modern {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.25rem;
    background: var(--white);
    border: 2px solid var(--gray-200);
    border-radius: var(--radius-base);
    color: var(--gray-700);
    font-weight: 500;
    cursor: pointer;
    transition: all var(--transition-base);
    gap: var(--space-sm);
}

.btn-filter-modern:hover {
    border-color: var(--primary-orange);
    color: var(--primary-orange);
    background: var(--primary-orange-light);
}

.dropdown-menu-modern {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 50;
    margin-top: 0.5rem;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-xl);
    min-width: 240px;
    opacity: 0;
    transform: translateY(-10px);
    visibility: hidden;
    transition: all var(--transition-base);
}

.filter-group-modern:hover .dropdown-menu-modern {
    opacity: 1;
    transform: translateY(0);
    visibility: visible;
}

.dropdown-header {
    padding: 1rem 1.25rem;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.dropdown-body {
    padding: 0.5rem;
}

.filter-option {
    display: flex;
    align-items: center;
    padding: 0.625rem 1rem;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: background var(--transition-fast);
}

.filter-option:hover {
    background: var(--gray-50);
}

.filter-option input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
    border: 2px solid var(--gray-300);
    border-radius: 0.25rem;
    margin-right: 0.75rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.filter-option input[type="checkbox"]:checked {
    background-color: var(--primary-orange);
    border-color: var(--primary-orange);
}

.filter-option label {
    cursor: pointer;
    color: var(--gray-700);
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    flex: 1;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    border-radius: var(--radius-xl);
    font-size: 0.75rem;
    font-weight: 600;
    gap: 0.25rem;
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: var(--secondary-green);
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.status-inactive {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger);
    border: 1px solid rgba(239, 68, 68, 0.2);
}

/* Tableau moderne */
.table-responsive-modern {
    overflow: hidden;
    border-radius: var(--radius-lg);
}

.table-modern {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.table-header-modern {
    background: var(--gray-50);
    padding: 1rem 1.5rem;
    border-bottom: 2px solid var(--gray-200);
    color: var(--gray-700);
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table-header-modern .d-flex {
    gap: 0.5rem;
}

.sort-btn {
    background: none;
    border: none;
    color: var(--gray-400);
    padding: 0.25rem;
    cursor: pointer;
    transition: color var(--transition-fast);
}

.sort-btn:hover {
    color: var(--primary-orange);
}

.table-row-modern {
    transition: all var(--transition-base);
    border-bottom: 1px solid var(--gray-100);
}

.table-row-modern:last-child {
    border-bottom: none;
}

.table-row-modern:hover {
    background: linear-gradient(90deg, var(--primary-orange-light) 0%, transparent 100%);
    transform: translateX(4px);
}

.table-row-modern td {
    padding: 1.25rem 1.5rem;
    vertical-align: middle;
}

/* Info agent */
.agent-info {
    display: flex;
    align-items: center;
    gap: var(--space-base);
}

.agent-avatar {
    flex-shrink: 0;
}

.avatar-img {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    object-fit: cover;
    border: 3px solid var(--white);
    box-shadow: var(--shadow-md);
}

.avatar-placeholder {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-lg);
    background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-green) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-weight: 600;
    font-size: 1rem;
    box-shadow: var(--shadow-md);
}

.agent-name {
    color: var(--gray-900);
    font-weight: 600;
    margin: 0;
    font-size: 1rem;
}

.agent-id {
    color: var(--gray-500);
    font-size: 0.75rem;
    margin: 0.25rem 0 0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Contact et email */
.contact-item, .email-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.contact-link, .email-link {
    color: var(--gray-700);
    text-decoration: none;
    transition: color var(--transition-fast);
    font-size: 0.9rem;
}

.contact-link:hover {
    color: var(--primary-orange);
}

.email-link:hover {
    color: var(--secondary-green);
}

.contact-urgent {
    color: var(--danger);
    font-size: 0.8rem;
    font-weight: 500;
}

/* Actions */
.actions-modern {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: var(--space-sm);
}

.btn-actions-modern {
    background: var(--gray-100);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: var(--radius-base);
    color: var(--gray-600);
    cursor: pointer;
    transition: all var(--transition-base);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-actions-modern:hover {
    background: var(--gray-200);
    color: var(--gray-900);
    transform: rotate(90deg);
}

.btn-action-modern {
    width: 36px;
    height: 36px;
    border-radius: var(--radius-base);
    border: 1px solid var(--gray-200);
    background: var(--white);
    color: var(--gray-600);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all var(--transition-base);
}

.btn-send-email:hover {
    background: var(--info);
    border-color: var(--info);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.btn-call:hover {
    background: var(--success);
    border-color: var(--success);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Dropdown actions */
.dropdown-actions-modern {
    position: relative;
}

.dropdown-actions-modern .dropdown-menu-modern {
    right: 0;
    left: auto;
    min-width: 200px;
}

.dropdown-item-modern {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: var(--gray-700);
    text-decoration: none;
    transition: all var(--transition-fast);
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    gap: var(--space-sm);
    font-size: 0.875rem;
}

.dropdown-item-modern:hover {
    background: var(--gray-50);
    color: var(--gray-900);
    padding-left: 1.25rem;
}

.dropdown-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 0.5rem 0;
}

/* État vide */
.empty-state-modern {
    text-align: center;
    padding: var(--space-xl) var(--space-lg);
}

.empty-icon {
    font-size: 4rem;
    background: linear-gradient(135deg, var(--primary-orange) 0%, var(--secondary-green) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: var(--space-md);
}

.empty-title {
    color: var(--gray-900);
    font-weight: 700;
    font-size: 1.5rem;
    margin-bottom: var(--space-sm);
}

.empty-text {
    color: var(--gray-500);
    margin-bottom: var(--space-lg);
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
}

/* Pagination */
.pagination-modern {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-lg) var(--space-xl);
    border-top: 1px solid var(--gray-200);
    margin-top: var(--space-md);
}

@media (min-width: 768px) {
    .pagination-modern {
        flex-direction: row;
    }
}

.pagination-info {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin-bottom: var(--space-md);
}

@media (min-width: 768px) {
    .pagination-info {
        margin-bottom: 0;
    }
}

/* Cartes statistiques */
.stat-card-modern {
    padding: var(--space-lg);
    display: flex;
    align-items: center;
    gap: var(--space-base);
    transition: all var(--transition-base);
    border-radius: var(--radius-lg);
    background: var(--white);
    border: 1px solid var(--gray-200);
}

.stat-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: var(--shadow-xl);
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: var(--white);
    flex-shrink: 0;
}

.stat-total {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-active {
    background: linear-gradient(135deg, var(--secondary-green) 0%, var(--accent-green) 100%);
}

.stat-recent {
    background: linear-gradient(135deg, var(--primary-orange) 0%, var(--primary-orange-dark) 100%);
}

.stat-locations {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

.stat-content {
    flex: 1;
}

.stat-value {
    color: var(--gray-900);
    font-weight: 800;
    font-size: 2rem;
    margin: 0;
    line-height: 1;
}

.stat-label {
    color: var(--gray-500);
    font-size: 0.875rem;
    margin: 0.5rem 0 0;
    font-weight: 500;
}

/* Modal */
.modern-modal {
    border: none;
    border-radius: var(--radius-xl);
    overflow: hidden;
    box-shadow: var(--shadow-2xl);
}

.modal-header {
    background: linear-gradient(90deg, var(--gray-50) 0%, var(--white) 100%);
    border-bottom: 1px solid var(--gray-200);
    padding: var(--space-lg);
}

.modal-title {
    color: var(--gray-900);
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--space-sm);
}

.btn-close-modal {
    background: var(--gray-100);
    border: none;
    width: 32px;
    height: 32px;
    border-radius: var(--radius-base);
    color: var(--gray-600);
    cursor: pointer;
    transition: all var(--transition-fast);
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close-modal:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

.modal-body {
    padding: var(--space-lg);
    color: var(--gray-700);
}

.modal-footer {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: var(--space-lg);
    display: flex;
    justify-content: flex-end;
    gap: var(--space-sm);
}

.btn-modal-secondary {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: var(--white);
    border: 2px solid var(--gray-300);
    border-radius: var(--radius-base);
    color: var(--gray-700);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    gap: var(--space-sm);
}

.btn-modal-secondary:hover {
    border-color: var(--gray-400);
    background: var(--gray-50);
}

.btn-modal-danger {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    border: none;
    border-radius: var(--radius-base);
    color: var(--white);
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    gap: var(--space-sm);
    box-shadow: var(--shadow-md);
}

.btn-modal-danger:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-row-modern {
    animation: fadeIn 0.6s ease-out forwards;
    animation-delay: calc(var(--index, 0) * 0.05s);
    opacity: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .page-header-modern {
        padding: var(--space-md);
    }
    
    .page-title {
        font-size: 1.75rem;
    }
    
    .agent-info {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--space-sm);
    }
    
    .actions-modern {
        flex-direction: column;
        align-items: flex-end;
    }
    
    .search-box-modern {
        min-width: 100%;
    }
    
    .modern-card {
        border-radius: var(--radius-lg);
    }
    
    .stat-card-modern {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Afficher les messages de session
    @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}',
            background: 'var(--light-bg)',
            color: 'var(--text-primary)',
            iconColor: 'var(--secondary-color)'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '{{ session('error') }}',
            confirmButtonColor: '#e94f1b',
            background: 'var(--light-bg)',
            color: 'var(--text-primary)'
        });
    @endif

    // Gestion de la recherche
    const searchInput = document.querySelector('.search-input-modern');
    const searchClear = document.querySelector('.search-clear');
    const tableRows = document.querySelectorAll('.table-row-modern');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    });

    // Gestion des filtres
    const filterButtons = document.querySelectorAll('.btn-filter-modern');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const dropdown = this.nextElementSibling;
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
    });

    // Fermer les dropdowns quand on clique ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.filter-group-modern')) {
            document.querySelectorAll('.dropdown-menu-modern').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
        
        if (!e.target.closest('.dropdown-actions-modern')) {
            document.querySelectorAll('.dropdown-actions-modern .dropdown-menu-modern').forEach(dropdown => {
                dropdown.style.display = 'none';
            });
        }
    });

    // Gestion des boutons d'action
    document.querySelectorAll('.btn-actions-modern').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        });
    });

    // Bouton d'appel
    document.querySelectorAll('.btn-call').forEach(button => {
        button.addEventListener('click', function() {
            const phone = this.getAttribute('data-phone');
            if (phone) {
                window.open(`tel:${phone}`, '_self');
            }
        });
    });

    // Bouton d'email
    document.querySelectorAll('.btn-send-email').forEach(button => {
        button.addEventListener('click', function() {
            const email = this.getAttribute('data-email');
            if (email) {
                window.open(`mailto:${email}`, '_self');
            }
        });
    });

    // Tri des colonnes
    document.querySelectorAll('.sort-btn').forEach(button => {
        button.addEventListener('click', function() {
            const sortBy = this.getAttribute('data-sort');
            alert(`Tri par ${sortBy} - Fonctionnalité à implémenter`);
        });
    });

    // Confirmation de suppression
    window.confirmDelete = function(agentId) {
        const form = document.getElementById('deleteForm');
        form.action = `/compagnie/agents/${agentId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };

    // Animation au chargement
    const tableRowsAnimated = document.querySelectorAll('.table-row-modern');
    tableRowsAnimated.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.05}s`;
        row.classList.add('animate__animated', 'animate__fadeIn');
    });

    // Export des données
    document.querySelector('.btn-export')?.addEventListener('click', function() {
        Swal.fire({
            title: 'Exporter les agents',
            text: 'Choisissez le format d\'export',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'CSV',
            cancelButtonText: 'Excel',
            showDenyButton: true,
            denyButtonText: 'PDF',
            confirmButtonColor: '#e94f1b',
            background: 'var(--light-bg)'
        }).then((result) => {
            if (result.isConfirmed) {
                Toast.fire({
                    icon: 'success',
                    title: 'Export CSV démarré'
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Toast.fire({
                    icon: 'success',
                    title: 'Export Excel démarré'
                });
            } else if (result.isDenied) {
                Toast.fire({
                    icon: 'success',
                    title: 'Export PDF démarré'
                });
            }
        });
    });
});
</script>

@endsection