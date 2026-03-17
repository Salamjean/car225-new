@extends('gare-espace.layouts.template')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="content-wrapper-modern">
    <div class="action-header animate__animated animate__fadeIn">
        <div class="header-left">
            <h1 class="main-title">Agents</h1>
            <p class="main-subtitle">Gérez les agents affectés à votre gare</p>
        </div>
        <div class="header-right">
            <a href="{{ route('gare-espace.agents.create') }}" class="btn btn-add-agent">
                <i class="fas fa-user-plus"></i>
                <span>Ajouter un agent</span>
            </a>
        </div>
    </div>


   <!-- Stats Cards (Format identique à la page Véhicules) -->
    <div class="row g-4 mb-4">
        <!-- Carte Total Agents -->
        <div class="col-md-4 animate__animated animate__fadeInUp">
            <div class="agent-stat-card">
                <div class="agent-stat-info">
                    <span class="agent-stat-label">TOTAL AGENTS</span>
                    <h3 class="agent-stat-value">{{ $agents->count() }}</h3>
                </div>
                <div class="agent-stat-icon bg-purple-light">
                    <i class="fas fa-user-friends text-purple"></i>
                </div>
            </div>
        </div>

        <!-- Carte Agents Actifs -->
        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
            <div class="agent-stat-card">
                <div class="agent-stat-info">
                    <span class="agent-stat-label">AGENTS ACTIFS</span>
                    <h3 class="agent-stat-value">{{ $agents->where('archived_at', null)->count() }}</h3>
                </div>
                <div class="agent-stat-icon bg-green-light">
                    <i class="fas fa-user-check text-green"></i>
                </div>
            </div>
        </div>

        <!-- Carte Agents Archivés -->
        <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
            <div class="agent-stat-card">
                <div class="agent-stat-info">
                    <span class="agent-stat-label">AGENTS ARCHIVÉS</span>
                    <h3 class="agent-stat-value">{{ $agents->whereNotNull('archived_at')->count() }}</h3>
                </div>
                <div class="agent-stat-icon bg-orange-light">
                    <i class="fas fa-box-archive text-orange"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="main-card-modern animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
        @if($agents->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-user-slash"></i>
                </div>
                <h3>Aucun agent trouvé</h3>
                <p>Commencez par ajouter un agent pour votre gare.</p>
                <a href="{{ route('gare-espace.agents.create') }}" class="btn btn-add-agent mt-3">
                    <i class="fas fa-user-plus"></i> Créer un agent
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Agent</th>
                            <th>Code ID</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Commune</th>
                            <th>Statut</th>
                            <th>Créé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                        <tr class="animate__animated animate__fadeIn">
                            <td>
                                <div class="agent-info">
                                    <div class="agent-avatar">
                                        @if($agent->profile_picture)
                                            <img src="{{ asset('storage/' . $agent->profile_picture) }}" alt="{{ $agent->name }}">
                                        @else
                                            <div class="avatar-placeholder">
                                                {{ strtoupper(substr($agent->name, 0, 1)) }}{{ strtoupper(substr($agent->prenom, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="agent-name">{{ $agent->name }} {{ $agent->prenom }}</p>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-secondary-light text-secondary-custom">{{ $agent->code_id ?? 'N/A' }}</span></td>
                            <td><span class="text-muted-custom">{{ $agent->email }}</span></td>
                            <td><span class="text-muted-custom">{{ $agent->contact }}</span></td>
                            <td><span class="text-muted-custom">{{ $agent->commune }}</span></td>
                            <td>
                                @if($agent->archived_at === null)
                                    <span class="badge-status active">
                                        <i class="fas fa-circle"></i> Actif
                                    </span>
                                @else
                                    <span class="badge-status archived">
                                        <i class="fas fa-circle"></i> Archivé
                                    </span>
                                @endif
                            </td>
                            <td><span class="text-muted-custom">{{ $agent->created_at->format('d/m/Y') }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<style>
:root {
    --primary: #e94f1b;
    --primary-light: #ff6b3d;
    --primary-dark: #c13e13;
    --gray-bg: #f8fafc;
    --card-bg: #ffffff;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --font-family: 'Plus Jakarta Sans', sans-serif;
    --radius-xl: 1.5rem;
}

.content-wrapper-modern {
    padding: 2rem;
    font-family: var(--font-family);
    max-width: 1400px;
    margin: 0 auto;
}

.action-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1.5rem;
}

.main-title {
    font-size: 2.25rem;
    font-weight: 800;
    color: var(--text-main);
    letter-spacing: -0.02em;
    margin-bottom: 0.25rem;
}

.main-subtitle {
    color: var(--text-muted);
    margin: 0;
}

.btn-add-agent {
    background: var(--primary);
    color: white !important;
    padding: 0.75rem 1.5rem;
    border-radius: 1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none !important;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(233, 79, 27, 0.2);
}

.btn-add-agent:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(233, 79, 27, 0.3);
}

/* --- DESIGN DES CARTES FORMAT "CUBE" --- */
.stat-card-v2 {
    background: #ffffff;
    border-radius: 1.25rem; /* Un poil plus arrondi */
    padding: 2.25rem 1.5rem; /* Beaucoup plus de padding vertical pour créer l'effet cube */
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    border: 1px solid #f8fafc;
    transition: all 0.3s ease;
    min-height: 150px; /* C'est cette ligne qui force la forme de cube (carrée) */
    height: 100%; /* S'assure que toutes les cartes ont la même taille */
}

.stat-card-v2:hover {
    transform: translateY(-5px); /* Animation un peu plus marquée au survol */
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-info .stat-label {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 0.5rem; /* Légèrement plus d'espace */
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.stat-info .stat-value {
    font-size: 2.25rem; /* Taille du chiffre plus imposante */
    font-weight: 800;
    color: #1e293b;
    margin: 0;
    line-height: 1;
}

.stat-icon-box {
    width: 60px; /* Rendu légèrement plus grand comme sur la photo 2 */
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    flex-shrink: 0;
}

/* Couleurs mappées depuis l'image 2 */
.bg-purple-light { background: #f3e8ff; }
.text-purple-primary { color: #8b5cf6; }

.bg-green-light { background: #dcfce7; }
.text-green-primary { color: #10b981; }

.bg-orange-light { background: #fef3c7; }
.text-orange-primary { color: #f59e0b; }
/* ----------------------------------------------- */

/* Table Card */
.main-card-modern {
    background: white;
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-color);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.table-modern {
    margin: 0;
    font-family: var(--font-family);
}

.table-modern thead th {
    background: #f8fafc;
    padding: 1rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border-color);
}

.table-modern tbody td {
    padding: 1rem 1.5rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}

.table-modern tbody tr:hover {
    background: #fafbfc;
}

.agent-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.agent-avatar {
    width: 42px;
    height: 42px;
    border-radius: 0.75rem;
    overflow: hidden;
    flex-shrink: 0;
}

.agent-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 700;
}

.agent-name {
    font-weight: 700;
    color: var(--text-main);
    margin: 0;
    font-size: 0.9rem;
}

.text-muted-custom {
    color: var(--text-muted);
    font-size: 0.85rem;
    font-weight: 500;
}

.bg-secondary-light {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
}

.text-secondary-custom {
    color: #475569;
    font-size: 0.85rem;
    font-weight: 700;
}

.badge-status {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.35rem 0.85rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 700;
}

.badge-status.active {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
}

.badge-status.active i { font-size: 0.4rem; }

.badge-status.archived {
    background: rgba(245, 158, 11, 0.1);
    color: #f59e0b;
}

.badge-status.archived i { font-size: 0.4rem; }

/* Empty State */
.empty-state {
    padding: 4rem 2rem;
    text-align: center;
}
/* --- DESIGN DES CARTES (Style identique à "Véhicules") --- */
.agent-stat-card {
    background: #ffffff;
    border-radius: 1rem; /* Bords arrondis de la carte */
    padding: 1.5rem; /* Espace interne */
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); /* Ombre douce */
    border: 1px solid #f1f5f9;
    height: 100%;
}

.agent-stat-info {
    display: flex;
    flex-direction: column;
}

.agent-stat-info .agent-stat-label {
    font-size: 0.75rem;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.agent-stat-info .agent-stat-value {
    font-size: 2rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
    line-height: 1;
}

.agent-stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 0.75rem; /* Carré avec bords arrondis */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

/* Couleurs des icônes de la page Agents */
.bg-purple-light { background-color: #f3e8ff; }
.text-purple { color: #9333ea; }

.bg-green-light { background-color: #dcfce7; }
.text-green { color: #16a34a; }

.bg-orange-light { background-color: #fef3c7; }
.text-orange { color: #d97706; }
/* -------------------------------------------------------- */
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

@media (max-width: 992px) {
    .content-wrapper-modern { padding: 1.25rem; }
    .main-title { font-size: 1.75rem; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: '{{ session('error') }}',
            confirmButtonColor: '#e94f1b'
        });
    @endif
});
</script>
@endsection