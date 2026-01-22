@extends('agent.layouts.template')

@section('content')
    <div class="mdc-layout-grid">
        <div class="mdc-layout-grid__inner">
            <div class="mdc-layout-grid__cell stretch-card mdc-layout-grid__cell--span-12">
                <div class="mdc-card p-4">
                    
                    <!-- En-tête -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="card-title mb-1">Recherche de Passager</h6>
                            <p class="text-muted mb-0">Recherchez une réservation par sa référence</p>
                        </div>
                        <a href="{{ route('agent.reservations.index') }}" class="btn btn-secondary">
                            <i class="material-icons mr-1" style="vertical-align: middle;">arrow_back</i>
                            Retour
                        </a>
                    </div>

                    <!-- Formulaire de recherche -->
                    <div class="row justify-content-center mb-4">
                        <div class="col-md-8">
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="material-icons">search</i>
                                    </span>
                                </div>
                                <input type="text" 
                                       id="searchInput" 
                                       class="form-control form-control-lg" 
                                       placeholder="Entrez la référence (ex: RES-20260120-XXXXX)"
                                       autofocus>
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-lg px-4" type="button" id="searchBtn">
                                        <i class="material-icons mr-1" style="vertical-align: middle;">search</i>
                                        Rechercher
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Résultat de recherche -->
                    <div id="searchResult" style="display: none;">
                        <div class="row justify-content-center">
                            <div class="col-md-10">
                                <div class="card shadow-lg border-0" id="resultCard">
                                    <!-- Le contenu sera injecté ici -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message si aucun résultat -->
                    <div id="noResult" class="text-center py-5" style="display: none;">
                        <i class="material-icons text-danger" style="font-size: 64px;">search_off</i>
                        <h5 class="mt-3 text-muted" id="noResultMessage">Aucune réservation trouvée</h5>
                    </div>

                    <!-- État initial -->
                    <div id="initialState" class="text-center py-5">
                        <i class="material-icons text-muted" style="font-size: 80px;">travel_explore</i>
                        <h5 class="mt-3 text-muted">Entrez une référence de réservation pour voir les détails</h5>
                        <p class="text-muted">Format: RES-AAAAMMJJ-XXXXXX</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<style>
    .info-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 2px;
    }
    .info-value {
        font-size: 1rem;
        color: #333;
        font-weight: 500;
    }
    .status-badge {
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .status-confirmee { background: #d4edda; color: #155724; }
    .status-terminee { background: #cce5ff; color: #004085; }
    .status-annulee { background: #f8d7da; color: #721c24; }
    .status-en_attente { background: #fff3cd; color: #856404; }
    
    .section-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #dee2e6;
    }
</style>

<script>
$(document).ready(function() {
    
    // Recherche au clic
    $('#searchBtn').click(function() {
        doSearch();
    });
    
    // Recherche à l'appui sur Entrée
    $('#searchInput').keypress(function(e) {
        if (e.which == 13) {
            doSearch();
        }
    });
    
    function doSearch() {
        var reference = $('#searchInput').val().trim();
        
        if (!reference) {
            alert('Veuillez entrer une référence');
            return;
        }
        
        // Masquer tout
        $('#initialState').hide();
        $('#searchResult').hide();
        $('#noResult').hide();
        
        // Afficher le loader
        $('#searchBtn').html('<span class="spinner-border spinner-border-sm mr-2"></span>Recherche...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("agent.reservations.search-by-reference") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                reference: reference
            },
            success: function(response) {
                if (response.success) {
                    showResult(response.reservation);
                }
            },
            error: function(xhr) {
                var message = 'Aucune réservation trouvée';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $('#noResultMessage').text(message);
                $('#noResult').show();
            },
            complete: function() {
                $('#searchBtn').html('<i class="material-icons mr-1" style="vertical-align: middle;">search</i>Rechercher').prop('disabled', false);
            }
        });
    }
    
    function showResult(r) {
        var statusClass = 'status-' + r.statut;
        var statusText = {
            'en_attente': 'En attente',
            'confirmee': 'Confirmée',
            'terminee': 'Embarquée',
            'annulee': 'Annulée'
        }[r.statut] || r.statut;
        
        var embarquementHtml = '';
        if (r.embarquement) {
            embarquementHtml = `
                <div class="col-12 mt-4">
                    <div class="section-title">
                        <i class="material-icons mr-2" style="vertical-align: middle; color: #28a745;">check_circle</i>
                        Détails de l'embarquement
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-label">Scanné le</div>
                            <div class="info-value">${r.embarquement.scanned_at}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Agent</div>
                            <div class="info-value">${r.embarquement.agent || 'N/A'}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-label">Véhicule</div>
                            <div class="info-value">${r.embarquement.vehicule || 'N/A'}</div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        var vehiculeHtml = '';
        if (r.vehicule_programme) {
            vehiculeHtml = `
                <div class="col-md-4">
                    <div class="info-label">Véhicule</div>
                    <div class="info-value">${r.vehicule_programme.marque} ${r.vehicule_programme.modele}</div>
                    <small class="text-muted">${r.vehicule_programme.immatriculation}</small>
                </div>
            `;
        }
        
        var html = `
            <div class="card-header bg-gradient-primary text-white py-3" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">Réservation</h5>
                        <h3 class="mb-0 font-weight-bold">${r.reference}</h3>
                    </div>
                    <span class="status-badge ${statusClass}">${statusText}</span>
                </div>
            </div>
            <div class="card-body">
                <!-- Infos Passager -->
                <div class="section-title">
                    <i class="material-icons mr-2" style="vertical-align: middle;">person</i>
                    Informations du passager
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-label">Nom complet</div>
                        <div class="info-value">${r.passager_prenom} ${r.passager_nom}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Téléphone</div>
                        <div class="info-value">${r.passager_telephone || 'N/A'}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Email</div>
                        <div class="info-value">${r.passager_email || 'N/A'}</div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-label">Contact d'urgence</div>
                        <div class="info-value">${r.passager_urgence || 'N/A'}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Place</div>
                        <div class="info-value">
                            <span class="badge badge-primary" style="font-size: 1.2rem; padding: 8px 16px;">${r.seat_number}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Montant</div>
                        <div class="info-value text-success font-weight-bold">${r.montant}</div>
                    </div>
                </div>
                
                <!-- Infos Voyage -->
                <div class="section-title">
                    <i class="material-icons mr-2" style="vertical-align: middle;">directions_bus</i>
                    Informations du voyage
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-label">Trajet</div>
                        <div class="info-value">${r.trajet}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Date</div>
                        <div class="info-value">${r.date_voyage}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Heure de départ</div>
                        <div class="info-value">${r.heure_depart}</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="info-label">Type</div>
                        <div class="info-value">${r.is_aller_retour ? 'Aller-Retour' : 'Aller Simple'}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Réservé le</div>
                        <div class="info-value">${r.created_at}</div>
                    </div>
                    ${vehiculeHtml}
                </div>
                
                ${embarquementHtml}
            </div>
        `;
        
        $('#resultCard').html(html);
        $('#searchResult').show();
    }
});
</script>
@endpush
