@extends('user.layouts.template')

@section('title', 'Mes Réservations')

@section('content')
    <div class="container-fluid py-4">
        <!-- En-tête -->
        <div class="row mb-6">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Mes Réservations</h1>
                        <p class="text-muted mb-0">Consultez et gérez toutes vos réservations de voyage</p>
                    </div>
                    <div>
                        <span class="badge bg-gradient-primary px-4 py-2">
                            <i class="fas fa-ticket-alt me-2"></i>
                            {{ $reservations->total() }} réservation(s)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-6">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Confirmées
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['confirmed'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    En attente
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['pending'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Annulées
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $stats['cancelled'] }}
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Montant total
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ number_format($stats['total_amount'], 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres de recherche -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold" style="color: #e94f1b;">
                                <i class="fas fa-filter me-2"></i>Filtres de recherche
                            </h6>
                            <button class="btn btn-sm btn-outline-secondary" type="button" id="toggleFilters">
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body" id="filtersSection">
                        <form method="GET" action="{{ route('reservation.index') }}" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="filter_reference" class="form-label small fw-bold">Référence</label>
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           id="filter_reference" 
                                           name="reference"
                                           value="{{ request('reference') }}"
                                           placeholder="Ex: RES-20260115-ABC123">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_statut" class="form-label small fw-bold">Statut</label>
                                    <select class="form-select form-select-sm" id="filter_statut" name="statut">
                                        <option value="">Tous les statuts</option>
                                        <option value="confirmee" {{ request('statut') == 'confirmee' ? 'selected' : '' }}>Confirmée</option>
                                        <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                        <option value="annulee" {{ request('statut') == 'annulee' ? 'selected' : '' }}>Annulée</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_date_voyage" class="form-label small fw-bold">Date du voyage</label>
                                    <input type="date" 
                                           class="form-control form-control-sm" 
                                           id="filter_date_voyage" 
                                           name="date_voyage"
                                           value="{{ request('date_voyage') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="filter_date_reservation" class="form-label small fw-bold">Date de réservation</label>
                                    <input type="date" 
                                           class="form-control form-control-sm" 
                                           id="filter_date_reservation" 
                                           name="created_at"
                                           value="{{ request('created_at') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="filter_compagnie" class="form-label small fw-bold">Compagnie</label>
                                    <input type="text" 
                                           class="form-control form-control-sm" 
                                           id="filter_compagnie" 
                                           name="compagnie"
                                           value="{{ request('compagnie') }}"
                                           placeholder="Nom de la compagnie">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold d-block">&nbsp;</label>
                                    <button type="submit" class="btn btn-sm btn-primary me-2">
                                        <i class="fas fa-search me-1"></i>Rechercher
                                    </button>
                                    <a href="{{ route('reservation.index') }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-redo me-1"></i>Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des réservations -->
        <div class="row">
            <div class="col-12">
                @if($reservations->isEmpty())
                    <div class="text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-ticket-alt fa-4x text-muted mb-4"></i>
                            <h3 class="text-gray-800 mb-2">Aucune réservation trouvée</h3>
                            <p class="text-muted mb-4">Vous n'avez pas encore effectué de réservation.</p>
                            <a href="{{ route('programme.index') }}" class="btn btn-primary"
                                style="background-color: #e94f1b; border-color: #e94f1b;">
                                <i class="fas fa-bus me-2"></i> Réserver un voyage
                            </a>
                        </div>
                    </div>
                @else
                    <div class="card shadow">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="border-0 text-center">Référence</th>
                                            <th class="border-0 text-center">Itinéraire</th>
                                            <th class="border-0 text-center">Date du voyage</th>
                                            <th class="border-0 text-center">Places</th>
                                            <th class="border-0 text-center">Passagers</th>
                                            <th class="border-0 text-center">Montant</th>
                                            <th class="border-0 text-center">Statut</th>
                                            <th class="border-0 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reservations as $reservation)
                                            <tr>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center"
                                                        style="display: flex; justify-content:center">
                                                        <div class="icon-shape icon-sm bg-light-primary rounded me-3">
                                                            <i class="fas fa-receipt text-primary"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">{{ $reservation->reference }}</span>
                                                            <div class="small text-muted">
                                                                {{ $reservation->created_at->format('d/m/Y H:i') }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center"
                                                        style="display: flex; justify-content:center">
                                                        <div class="icon-shape icon-sm bg-light-info rounded me-3">
                                                            <i class="fas fa-route text-info"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">
                                                                {{ $reservation->programme->point_depart }} →
                                                                {{ $reservation->programme->point_arrive }}
                                                            </span>
                                                            <div class="small text-muted">
                                                                {{ $reservation->programme->compagnie->name ?? 'N/A' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center"
                                                        style="display: flex; justify-content:center">
                                                        <div class="icon-shape icon-sm bg-light-warning rounded me-3">
                                                            <i class="fas fa-calendar text-warning"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">{{ $reservation->date_voyage }}</span>
                                                            <div class="small text-muted">
                                                                {{ date('H:i', strtotime($reservation->programme->heure_depart)) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center"
                                                        style="display: flex; justify-content:center">
                                                        <div class="icon-shape icon-sm bg-light-success rounded me-3">
                                                            <i class="fas fa-chair text-success"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold">Place N° {{ $reservation->seat_number }}</span>
                                                            @if($reservation->is_aller_retour)
                                                                <div class="small text-blue-600 fw-bold">
                                                                    <i class="fas fa-exchange-alt"></i> Aller-Retour
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @php
                                                        $passagers = is_array($reservation->passagers) ? $reservation->passagers : json_decode($reservation->passagers, true) ?? [];
                                                    @endphp
                                                    @if(!empty($passagers))
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-info view-passengers-btn" 
                                                                data-passengers='@json($passagers)'
                                                                data-reference="{{ $reservation->reference }}">
                                                            <i class="fas fa-users"></i> {{ count($passagers) }} passager(s)
                                                        </button>
                                                    @else
                                                        <span class="text-muted small">Aucune info</span>
                                                    @endif
                                                </td>
                                                <td class="align-middle">
                                                    <div class="d-flex align-items-center"
                                                        style="display: flex; justify-content:center">
                                                        <div class="icon-shape icon-sm bg-light-danger rounded me-3">
                                                            <i class="fas fa-money-bill text-danger"></i>
                                                        </div>
                                                        <div>
                                                            <span
                                                                class="fw-bold">{{ number_format($reservation->montant, 0, ',', ' ') }}
                                                                FCFA</span>
                                                            <div class="small text-muted">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle" style="display: flex; justify-content:center">
                                                    @if($reservation->statut == 'confirmee')
                                                        <span
                                                            class="badge bg-warning bg-opacity-10 text-white border border-warning border-opacity-25 px-3 py-2">
                                                            <i class="fas fa-check-circle me-1"></i> Confirmée
                                                        </span>
                                                    @elseif($reservation->statut == 'terminee')
                                                        <span
                                                            class="badge bg-success bg-opacity-10 text-white border border-success border-opacity-25 px-3 py-2">
                                                            <i class="fas fa-clock me-1"></i> Terminée
                                                        </span>
                                                    @else
                                                       <span
                                                            class="badge bg-warning bg-opacity-10 text-white border border-warning border-opacity-25 px-3 py-2">
                                                            <i class="fas fa-check-circle me-1"></i> Confirmée
                                                        </span>
                                                    @endif

                                                    @if($reservation->embarquement_status == 'scanned')
                                                        <div class="mt-1">
                                                            <span
                                                                class="badge bg-info bg-opacity-10 text-white border border-info border-opacity-25 px-2 py-1 small">
                                                                <i class="fas fa-qrcode me-1"></i> Emb. validé
                                                            </span>
                                                        </div>
                                                    @endif
                                                </td>
                                              <td class="align-middle text-center">
    <div class="btn-group" role="group">
        <a href="{{ route('reservations.show', $reservation->id) }}"
            class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip"
            title="Voir les détails">
            <i class="fas fa-eye"></i>
        </a>

        {{-- Bouton PDF ALLER --}}
        @if($reservation->qr_code_path)
            @php
                // Le bouton est désactivé UNIQUEMENT si le statut spécifique de l'aller est 'terminee'
                // Ou si le statut global est 'terminee' (sécurité supplémentaire)
                $allerConsomme = $reservation->statut_aller === 'terminee' || $reservation->statut === 'terminee';
            @endphp
            
            <button type="button" 
                    class="btn btn-sm {{ $allerConsomme ? 'btn-secondary disabled' : 'btn-outline-success' }} download-ticket-btn"
                    data-id="{{ $reservation->id }}"
                    data-type="aller"
                    data-reference="{{ $reservation->reference }}"
                    data-url="{{ route('reservations.ticket', ['reservation' => $reservation->id, 'type' => 'aller']) }}"
                    data-toggle="tooltip" 
                    title="{{ $allerConsomme ? 'Voyage ALLER terminé' : 'Télécharger billet ALLER' }}"
                    {{ $allerConsomme ? 'disabled' : '' }}
                    style="{{ $allerConsomme ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                <i class="fas fa-file-pdf"></i>
                @if($reservation->is_aller_retour)
                    Aller
                @else
                    Billet
                @endif
            </button>
        @endif

        {{-- Bouton PDF RETOUR (seulement si aller-retour) --}}
        @if($reservation->is_aller_retour)
            @php
                // Le bouton est désactivé si le statut spécifique du retour est 'terminee'
                $retourConsomme = $reservation->statut_retour === 'terminee';
            @endphp
            
            <button type="button" 
                    class="btn btn-sm {{ $retourConsomme ? 'btn-secondary disabled' : 'btn-outline-warning' }} download-ticket-btn"
                    data-id="{{ $reservation->id }}"
                    data-type="retour"
                    data-reference="{{ $reservation->reference }}"
                    data-url="{{ route('reservations.ticket', ['reservation' => $reservation->id, 'type' => 'retour']) }}"
                    data-toggle="tooltip" 
                    title="{{ $retourConsomme ? 'Voyage RETOUR terminé' : 'Télécharger billet RETOUR' }}"
                    {{ $retourConsomme ? 'disabled' : '' }}
                    style="{{ $retourConsomme ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                <i class="fas fa-file-pdf"></i> Retour
            </button>
        @endif

        @if($reservation->statut == 'en_attente')
            <button type="button"
                class="btn btn-sm btn-outline-danger cancel-reservation-btn"
                data-id="{{ $reservation->id }}"
                data-reference="{{ $reservation->reference }}"
                data-cancel-url="#" {{-- Assure-toi de mettre ta route ici --}}
                data-bs-toggle="tooltip" title="Annuler la réservation">
                <i class="fas fa-times"></i>
            </button>
        @endif
    </div>
</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($reservations->hasPages())
                            <div class="card-footer border-0 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        Affichage de {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur
                                        {{ $reservations->total() }} réservations
                                    </div>
                                    <nav aria-label="Page navigation">
                                        {{ $reservations->links() }}
                                    </nav>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #e94f1b;
            --primary-light: rgba(254, 162, 25, 0.1);
            --primary-dark: #e89116;
            --success-color: #10b981;
            --success-light: rgba(16, 185, 129, 0.1);
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
        }

        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: 10px 10px 0 0 !important;
        }

        .table th {
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-top: none;
            padding: 1rem 0.75rem;
            background-color: #f9fafb;
        }

        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #f3f4f6;
        }

        .table tr {
            transition: background-color 0.2s ease;
        }

        .table tr:hover {
            background-color: var(--primary-light);
        }

        .icon-shape {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(254, 162, 25, 0.3);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .empty-state {
            padding: 3rem;
        }

        .empty-state i {
            color: #d1d5db;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #ff9900 100%) !important;
        }

        .border-left-primary {
            border-left: 4px solid var(--primary-color) !important;
        }

        .border-left-warning {
            border-left: 4px solid var(--warning-color) !important;
        }

        .border-left-danger {
            border-left: 4px solid var(--danger-color) !important;
        }

        .border-left-success {
            border-left: 4px solid var(--success-color) !important;
        }

        /* Bouton passagers */
        .btn-outline-info {
            color: #3b82f6;
            border-color: #3b82f6;
            transition: all 0.3s ease;
        }

        .btn-outline-info:hover {
            background-color: #3b82f6;
            border-color: #3b82f6;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .view-passengers-btn {
            font-weight: 500;
        }

        /* Filtres */
        #filtersSection {
            transition: all 0.3s ease;
        }

        #toggleFilters {
            transition: all 0.3s ease;
        }

        #toggleFilters:hover {
            transform: scale(1.05);
        }

        /* Statut badges */
        .badge.bg-success {
            background-color: var(--success-color) !important;
        }

        .badge.bg-warning {
            background-color: var(--warning-color) !important;
        }

        .badge.bg-danger {
            background-color: var(--danger-color) !important;
        }

        /* Animation pour les badges */
        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        .badge.bg-success {
            animation: pulse 2s infinite;
        }

        /* Pagination personnalisée */
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .pagination .page-link {
            color: var(--primary-color);
            border: 1px solid #e5e7eb;
            margin: 0 2px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: var(--primary-light);
            border-color: var(--primary-color);
        }

        /* SweetAlert2 personnalisation */
        .swal2-popup {
            border-radius: 12px !important;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif !important;
        }

        .swal2-title {
            color: #1f2937 !important;
            font-size: 1.5rem !important;
            font-weight: 600 !important;
            margin-bottom: 1rem !important;
        }

        .swal2-html-container {
            color: #6b7280 !important;
            font-size: 1rem !important;
            line-height: 1.5 !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 0.75rem 2rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
        }

        .swal2-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(254, 162, 25, 0.3) !important;
        }

        .swal2-cancel {
            border-radius: 8px !important;
            padding: 0.75rem 2rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
        }

        .swal2-cancel:hover {
            transform: translateY(-1px) !important;
        }

        .swal2-success {
            border-color: var(--success-color) !important;
        }

        .swal2-warning {
            border-color: var(--warning-color) !important;
        }

        .swal2-error {
            border-color: var(--danger-color) !important;
        }

        .swal2-icon.swal2-success [class^=swal2-success-line] {
            background-color: var(--success-color) !important;
        }

        .swal2-icon.swal2-success .swal2-success-ring {
            border-color: rgba(16, 185, 129, 0.3) !important;
        }

        /* QR Code Modal Style */
        .qr-modal-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem;
        }

        .qr-code-img {
            width: 200px;
            height: 200px;
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            padding: 10px;
            background: white;
            margin-bottom: 1rem;
        }

        .embarquement-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.875rem;
            margin-top: 1rem;
        }

        .embarquement-status.validated {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }

        .embarquement-status.pending {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                border: none;
            }

            .icon-shape {
                width: 32px;
                height: 32px;
            }

            .btn-group .btn {
                padding: 0.25rem 0.5rem;
            }

            .swal2-popup {
                width: 90% !important;
                margin: 1rem !important;
            }
        }
    </style>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fonction de test SweetAlert2
        function testSwal() {
            if (typeof Swal === 'undefined') {
                alert('SweetAlert2 n\'est PAS chargé !');
                return;
            }
            
            Swal.fire({
                title: 'Test réussi !',
                text: 'SweetAlert2 fonctionne correctement',
                icon: 'success',
                confirmButtonText: 'Super !'
            });
        }
        
        document.addEventListener('DOMContentLoaded', function () {
            // Initialiser les tooltips (Version Bootstrap 4 / jQuery)
            if (typeof $ !== 'undefined' && $.fn.tooltip) {
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-bs-toggle="tooltip"]').tooltip(); // Pour compatibilité si des attributs BS5 traînent
            } else {
                console.warn('jQuery ou Bootstrap Tooltip non chargé');
            }

            // Configuration globale SweetAlert2
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

            // Vérifier que SweetAlert2 est chargé
            console.log('SweetAlert2 disponible:', typeof Swal !== 'undefined');

            // Gérer l'affichage des passagers avec délégation d'événements
            document.addEventListener('click', function(e) {
                // Vérifier si l'élément cliqué ou son parent est le bouton passagers
                const button = e.target.closest('.view-passengers-btn');
                
                if (button) {
                    e.preventDefault();
                    e.stopPropagation();
                    console.log('Bouton passagers cliqué via délégation');
                    
                    // Vérifier que Swal est disponible
                    if (typeof Swal === 'undefined') {
                        console.error('SweetAlert2 n\'est pas chargé !');
                        alert('Erreur: SweetAlert2 n\'est pas disponible');
                        return;
                    }
                    
                    const passengersData = button.getAttribute('data-passengers');
                    const reference = button.getAttribute('data-reference');
                    
                    console.log('Données brutes:', passengersData);
                    console.log('Référence:', reference);
                    
                    if (!passengersData) {
                        console.error('Pas de données passagers');
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Aucune donnée de passagers disponible'
                        });
                        return;
                    }
                    
                    let passengers;
                    try {
                        passengers = JSON.parse(passengersData);
                        console.log('Passagers parsés:', passengers);
                    } catch (error) {
                        console.error('Erreur de parsing JSON:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de charger les informations des passagers'
                        });
                        return;
                    }

                    let htmlContent = `
                        <div style="text-align: left; max-height: 500px; overflow-y: auto;">
                            <div style="background: linear-gradient(135deg, #e94f1b 0%, #e89116 100%); color: white; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                                <h5 style="margin: 0; font-weight: bold;">
                                    <i class="fas fa-users"></i> Liste des Passagers
                                </h5>
                                <p style="margin: 5px 0 0 0; font-size: 0.9rem; opacity: 0.9;">
                                    Réservation: ${reference}
                                </p>
                            </div>
                    `;

                    passengers.forEach((passenger, index) => {
                        htmlContent += `
                            <div style="background: #f9fafb; border-left: 4px solid #e94f1b; padding: 15px; margin-bottom: 15px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <h6 style="margin: 0; color: #1f2937; font-weight: bold;">
                                        <i class="fas fa-user-circle" style="color: #e94f1b;"></i> 
                                        ${passenger.prenom || ''} ${passenger.nom || ''}
                                    </h6>
                                    <span style="background: #e94f1b; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: bold;">
                                        <i class="fas fa-chair"></i> Place ${passenger.seat_number || 'N/A'}
                                    </span>
                                </div>
                                <div style="color: #6b7280; font-size: 0.9rem; line-height: 1.8;">
                                    <div style="margin-bottom: 5px;">
                                        <i class="fas fa-envelope" style="color: #3b82f6; width: 20px;"></i>
                                        <strong>Email:</strong> ${passenger.email || 'N/A'}
                                    </div>
                                    <div style="margin-bottom: 5px;">
                                        <i class="fas fa-phone" style="color: #10b981; width: 20px;"></i>
                                        <strong>Téléphone:</strong> ${passenger.telephone || 'N/A'}
                                    </div>
                                    <div>
                                        <i class="fas fa-exclamation-triangle" style="color: #ef4444; width: 20px;"></i>
                                        <strong>Contact d'urgence:</strong> ${passenger.urgence || 'N/A'}
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    htmlContent += `</div>`;

                    console.log('Affichage du popup');
                    Swal.fire({
                        html: htmlContent,
                        width: 700,
                        padding: '2rem',
                        showCloseButton: true,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'animated fadeIn'
                        }
                    });
                }
            });

            // Gérer le téléchargement des tickets (téléchargement direct)
            document.addEventListener('click', function(e) {
                const button = e.target.closest('.download-ticket-btn');
                
                if (button) {
                    e.preventDefault();
                    e.stopPropagation();
                    const baseUrl = button.getAttribute('data-url');
                    
                    if (baseUrl) {
                        window.open(baseUrl, '_blank');
                    }
                }
            });

            // Toggle des filtres
            const toggleFiltersBtn = document.getElementById('toggleFilters');
            const filtersSection = document.getElementById('filtersSection');
            
            if (toggleFiltersBtn && filtersSection) {
                // Cacher les filtres par défaut
                filtersSection.style.display = 'none';
                
                toggleFiltersBtn.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (filtersSection.style.display === 'none') {
                        filtersSection.style.display = 'block';
                        icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                    } else {
                        filtersSection.style.display = 'none';
                        icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                    }
                });
            }

            // Gérer l'affichage des QR Codes
            document.querySelectorAll('.show-qr-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const reservationId = this.dataset.id;
                    const reference = this.dataset.reference;
                    const qrPath = this.dataset.qrPath;
                    const embarquementStatus = this.dataset.embarquementStatus;
                    const scannedAt = this.dataset.scannedAt;
                    const downloadUrl = this.dataset.downloadUrl;

                    let htmlContent = `
                    <div class="qr-modal-content">
                        <img src="${qrPath}" 
                             alt="QR Code" 
                             class="qr-code-img"
                             onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${reference}'">

                        <h5 class="mt-3 mb-2" style="color: #1f2937;">${reference}</h5>
                        <p class="text-muted small text-center mb-4">
                            Présentez ce code à l'embarquement<br>
                            ou téléchargez-le pour l'avoir hors ligne
                        </p>`;

                    if (embarquementStatus === 'scanned' && scannedAt) {
                        htmlContent += `
                        <div class="embarquement-status validated">
                            <i class="fas fa-check-circle"></i>
                            Validé le ${scannedAt}
                        </div>`;
                    } else {
                        htmlContent += `
                        <div class="embarquement-status pending">
                            <i class="fas fa-clock"></i>
                            En attente de validation
                        </div>`;
                    }

                    htmlContent += `</div>`;

                    Swal.fire({
                        title: 'QR Code d\'embarquement',
                        html: htmlContent,
                        width: 500,
                        padding: '2rem',
                        showCloseButton: true,
                        showCancelButton: true,
                        confirmButtonText: '<i class="fas fa-download me-2"></i> Télécharger',
                        cancelButtonText: 'Fermer',
                        buttonsStyling: true,
                        customClass: {
                            confirmButton: 'swal2-confirm',
                            cancelButton: 'swal2-cancel'
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeIn animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut animate__faster'
                        },
                        didOpen: () => {
                            // Ajouter des animations au QR code
                            const qrImg = document.querySelector('.qr-code-img');
                            if (qrImg) {
                                qrImg.style.transition = 'transform 0.3s ease';
                                qrImg.addEventListener('mouseenter', () => {
                                    qrImg.style.transform = 'scale(1.05)';
                                });
                                qrImg.addEventListener('mouseleave', () => {
                                    qrImg.style.transform = 'scale(1)';
                                });
                            }
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(downloadUrl, '_blank');
                        }
                    });
                });
            });

            // Gérer l'annulation des réservations
            document.querySelectorAll('.cancel-reservation-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const reservationId = this.dataset.id;
                    const reference = this.dataset.reference;
                    const cancelUrl = this.dataset.cancelUrl;
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    Swal.fire({
                        title: 'Êtes-vous sûr ?',
                        html: `
                        <div style="text-align: left;">
                            <p>Vous êtes sur le point d'annuler la réservation :</p>
                            <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
                                <strong style="color: #ef4444;">${reference}</strong>
                            </div>
                            <div style="color: #ef4444; font-size: 0.875rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                Cette action est irréversible.
                            </div>
                        </div>
                    `,
                        icon: 'warning',
                        iconColor: '#f59e0b',
                        showCancelButton: true,
                        confirmButtonText: 'Oui, annuler',
                        cancelButtonText: 'Non, garder',
                        reverseButtons: true,
                        buttonsStyling: true,
                        customClass: {
                            confirmButton: 'swal2-confirm',
                            cancelButton: 'swal2-cancel'
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeIn animate__faster'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOut animate__faster'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Créer un formulaire dynamique pour l'annulation
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = cancelUrl;
                            form.style.display = 'none';

                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = csrfToken;

                            const methodInput = document.createElement('input');
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'DELETE';

                            form.appendChild(csrfInput);
                            form.appendChild(methodInput);
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });

            // Auto-collapse des filtres sur mobile
            if (window.innerWidth < 768) {
                const filterCollapse = document.getElementById('filterCollapse');
                if (filterCollapse) {
                    filterCollapse.classList.remove('show');
                }
            }

            // Animation pour les nouvelles réservations
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                Toast.fire({
                    icon: 'success',
                    title: 'Réservation confirmée avec succès !',
                    text: 'Votre billet est disponible.',
                    iconColor: '#10b981',
                    background: 'linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%)'
                });

                // Supprimer le paramètre de l'URL
                history.replaceState({}, document.title, window.location.pathname);
            }

            // Afficher/masquer les filtres avec animation
            const filterToggle = document.getElementById('filterToggle');
            if (filterToggle) {
                filterToggle.addEventListener('click', function () {
                    const icon = this.querySelector('i');
                    if (icon.classList.contains('fa-filter')) {
                        icon.classList.replace('fa-filter', 'fa-times');
                    } else {
                        icon.classList.replace('fa-times', 'fa-filter');
                    }
                });
            }

            // Gestion des erreurs
            if (urlParams.has('error')) {
                const errorType = urlParams.get('error');
                let errorTitle = 'Erreur';
                let errorText = 'Une erreur est survenue.';

                switch (errorType) {
                    case 'cancellation_failed':
                        errorTitle = 'Annulation impossible';
                        errorText = 'Cette réservation ne peut plus être annulée.';
                        break;
                    case 'ticket_unavailable':
                        errorTitle = 'Billet indisponible';
                        errorText = 'Le billet n\'est pas encore disponible pour cette réservation.';
                        break;
                }

                Swal.fire({
                    title: errorTitle,
                    text: errorText,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    iconColor: '#ef4444',
                    customClass: {
                        confirmButton: 'swal2-confirm'
                    }
                });

                // Supprimer le paramètre d'erreur de l'URL
                history.replaceState({}, document.title, window.location.pathname);
            }
        });
    </script>

@endsection