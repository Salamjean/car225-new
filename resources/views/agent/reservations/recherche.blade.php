@extends('agent.layouts.template')

@section('content')
    <div class="container-fluid content-wrapper">
        <!-- Style personnalisé pour cette vue uniquement -->
        <style>
            :root {
                --primary-brand: #ff5a1f;
                --primary-gradient: linear-gradient(135deg, #001a41 0%, #003380 100%);
                --secondary-brand: #001a41;
                --orange-brand: #ff5a1f;
                --bg-soft: #f8fafc;
                --success-accent: #22c55e;
                --danger-accent: #ef4444;
                --info-accent: #3b82f6;
            }

            .content-wrapper {
                background-color: var(--bg-soft);
                min-height: calc(100vh - 64px);
                padding: 2rem;
            }

            /* Adaptations pour Tablettes et Écrans Intermédiaires (iPad Pro, Petits Laptops) */
            @media (max-width: 1224px) {
                .content-wrapper {
                    padding: 1.5rem;
                }
                .search-section {
                    padding: 2.5rem !important;
                    border-radius: 28px !important;
                }
                .search-title {
                    font-size: 1.6rem !important;
                }
                .search-input-container {
                    padding: 0.4rem 0.4rem 0.4rem 1rem;
                    gap: 0.5rem;
                    min-height: 56px;
                }
                .search-input-container input {
                    font-size: 1rem !important;
                    height: 44px;
                }
                .btn-premium-search {
                    font-size: 0.95rem;
                    padding: 0 1.25rem !important;
                    height: 44px;
                }
            }

            /* Adaptations pour Mobiles et Tablettes en portrait */
            @media (max-width: 991px) {
                .search-section {
                    padding: 2rem !important;
                    border-radius: 24px !important;
                }
                .search-title {
                    font-size: 1.5rem !important;
                }
                .search-input-container {
                    flex-direction: column !important;
                    background: transparent !important;
                    padding: 0 !important;
                    gap: 12px !important;
                    min-height: auto !important;
                    border: none !important;
                }
                .search-input-container:focus-within {
                    box-shadow: none !important;
                }
                .search-input-container input {
                    background: #f1f5f9 !important;
                    width: 100% !important;
                    padding: 1.25rem !important;
                    border-radius: 18px !important;
                    text-align: center;
                    border: 2px solid #e2e8f0 !important;
                    height: 60px !important;
                    font-size: 1.1rem !important;
                    transition: all 0.3s ease;
                }
                .search-input-container input:focus {
                    background: white !important;
                    border-color: var(--primary-brand) !important;
                    box-shadow: 0 10px 20px -5px rgba(249, 115, 22, 0.1) !important;
                }
                .search-input-container > i {
                    display: none;
                }
                .btn-premium-search {
                    width: 100% !important;
                    justify-content: center;
                    padding: 0 !important;
                    height: 60px !important;
                    border-radius: 18px !important;
                    font-size: 1.1rem !important;
                }
                .d-flex.justify-content-between.align-items-center.mb-4.px-2 {
                    flex-direction: column;
                    text-align: center;
                    gap: 1.5rem;
                }
                .btn-back {
                    width: 100%;
                    justify-content: center;
                }
            }

            .premium-card {
                background: white;
                border-radius: 24px;
                border: 1px solid rgba(249, 115, 22, 0.1);
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .search-section {
                background: white;
                border-radius: 32px;
                padding: 3rem;
                box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(249, 115, 22, 0.08);
                margin-bottom: 2rem;
            }

            .search-title {
                font-family: 'Inter', sans-serif;
                font-weight: 800;
                color: var(--secondary-brand);
                font-size: 1.75rem;
                letter-spacing: -0.025em;
                margin-bottom: 0.5rem;
            }

            .search-input-container {
                display: flex;
                align-items: center;
                gap: 1rem;
                background: #f1f5f9;
                padding: 0.5rem 0.5rem 0.5rem 1.5rem;
                border-radius: 40px;
                border: 2px solid transparent;
                transition: all 0.3s ease;
                min-height: 64px;
            }

            .search-input-container:focus-within {
                background: white;
                border-color: var(--primary-brand);
                box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
            }

            .search-input-container input {
                border: none;
                background: transparent !important;
                flex: 1;
                font-weight: 700;
                font-family: 'JetBrains Mono', monospace;
                font-size: 1.1rem;
                height: 48px;
                color: var(--secondary-brand);
            }

            .search-input-container input:focus {
                outline: none;
            }

            .btn-premium-search {
                background: linear-gradient(135deg, var(--orange-brand) 0%, #e64e16 100%);
                color: white !important;
                border: none;
                height: 48px;
                padding: 0 2rem;
                border-radius: 30px;
                font-weight: 700;
                display: flex;
                align-items: center;
                gap: 0.75rem;
                box-shadow: 0 10px 15px -3px rgba(255, 90, 31, 0.3);
                transition: all 0.3s ease;
                white-space: nowrap;
            }

            .btn-premium-search:hover {
                transform: translateY(-2px);
                box-shadow: 0 20px 25px -5px rgba(255, 90, 31, 0.5);
            }

            @keyframes pulse {
                0% { transform: scale(1); opacity: 0.8; }
                50% { transform: scale(1.05); opacity: 1; }
                100% { transform: scale(1); opacity: 0.8; }
            }

            .pulse-icon {
                animation: pulse 3s infinite ease-in-out;
            }

            .btn-back {
                background: white;
                color: var(--secondary-brand) !important;
                border: 1px solid #e2e8f0;
                padding: 0.5rem 1.25rem;
                border-radius: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                transition: all 0.3s ease;
            }

            .btn-back:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
            }

            /* Styles pour les résultats */
            .result-card {
                border-radius: 24px;
                border: none;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
                overflow: hidden;
                animation: fadeInUp 0.5s ease-out forwards;
            }

            @keyframes fadeInUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .result-header {
                background: var(--secondary-brand);
                color: white;
                padding: 1.5rem 2rem;
            }

            @media (max-width: 768px) {
                .result-header {
                    flex-direction: column !important;
                    text-align: center;
                    padding: 1.5rem 1rem;
                    gap: 1rem;
                }
                .result-header .text-right {
                    text-align: center !important;
                    align-items: center !important;
                }
                .info-group {
                    padding: 1rem !important;
                    border-radius: 15px !important;
                }
                .info-value {
                    font-size: 0.9rem !important;
                }
            }

            .info-group {
                background: #f8fafc;
                border-radius: 20px;
                padding: 1.5rem;
                border: 1px solid #f1f5f9;
                height: 100%;
            }

            .info-label {
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #94a3b8;
                font-weight: 600;
                margin-bottom: 0.25rem;
            }

            .info-value {
                font-weight: 700;
                color: var(--secondary-brand);
                font-size: 1rem;
            }

            .section-title-premium {
                font-size: 0.85rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.1em;
                color: var(--primary-brand);
                margin-bottom: 1.25rem;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .status-badge-premium {
                padding: 6px 16px;
                border-radius: 100px;
                font-weight: 800;
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }

            .status-confirmee { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2); }
            .status-terminee { background: rgba(59, 130, 246, 0.1); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.2); }
            .status-annulee { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239, 68, 68, 0.2); }
            .status-en_attente { background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.2); }

            /* Table des récents */
            .recent-table-card {
                background: white;
                border-radius: 24px;
                border: 1px solid #e2e8f0;
                overflow: hidden;
            }

            @media (max-width: 768px) {
                .table-premium thead {
                    display: none;
                }
                .table-premium tbody td {
                    display: block;
                    width: 100%;
                    text-align: right;
                    padding: 0.5rem 1rem !important;
                    border-bottom: none !important;
                    position: relative;
                }
                .table-premium tbody td:before {
                    content: attr(data-label);
                    position: absolute;
                    left: 1rem;
                    font-weight: 700;
                    color: #94a3b8;
                    text-transform: uppercase;
                    font-size: 0.7rem;
                }
                .table-premium tbody td:last-child {
                    border-bottom: 1px solid #f1f5f9 !important;
                    padding-bottom: 1.5rem !important;
                }
                .table-premium tbody tr {
                    display: block;
                    padding-top: 1rem;
                }
            }

            .table-premium thead th {
                background: var(--secondary-brand);
                border-bottom: 2px solid rgba(255, 255, 255, 0.1);
                color: white;
                font-weight: 700;
                text-transform: uppercase;
                font-size: 0.75rem;
                padding: 1.25rem 1.5rem;
            }

            .table-premium tbody td {
                padding: 1.25rem 1.5rem;
                vertical-align: middle;
                color: var(--secondary-brand);
                font-weight: 500;
            }

            .ref-badge {
                background: #f1f5f9;
                color: var(--secondary-brand);
                font-family: 'JetBrains Mono', monospace;
                font-weight: 700;
                padding: 4px 10px;
                border-radius: 8px;
                font-size: 0.85rem;
            }

            .pulse-icon {
                animation: pulse-soft 2s infinite;
            }

            @keyframes pulse-soft {
                0% { transform: scale(1); opacity: 0.8; }
                50% { transform: scale(1.1); opacity: 1; }
                100% { transform: scale(1); opacity: 0.8; }
            }
        </style>

        <div class="row justify-content-center">
            <div class="col-xl-10 col-12">
                
                <!-- En-tête avec navigation -->
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <div>
                        <h2 class="search-title">Recherche de Réservation</h2>
                        <p class="text-slate-500 mb-0">Consultez les détails complets d'une réservation par sa référence.</p>
                    </div>
                    <a href="{{ route('agent.reservations.index') }}" class="btn-back">
                        <i class="material-icons">arrow_back</i>
                        Retour au Scanner
                    </a>
                </div>

                <!-- Section Recherche -->
                <div class="search-section">
                    <div class="row align-items-center">
                        <div class="col-xl-3 text-center d-none d-xl-block">
                            <div class="p-4 bg-orange-50 rounded-full inline-flex align-items-center justify-content-center">
                                <i class="material-icons text-[#ff5a1f]" style="font-size: 64px;">manage_search</i>
                            </div>
                        </div>
                        <div class="col-xl-9 col-12">
                            <div class="search-input-container mb-3">
                                <i class="material-icons text-slate-400 mt-1">search</i>
                                <input type="text" 
                                       id="searchInput" 
                                       placeholder="ENTREZ LA RÉFÉRENCE (EX: RES-20260318-XXXXX)"
                                       autocomplete="off"
                                       class="text-uppercase"
                                       autofocus>
                                <button class="btn-premium-search" type="button" id="searchBtn">
                                    <span>Rechercher</span>
                                    <i class="material-icons">east</i>
                                </button>
                            </div>
                            <p class="text-slate-400 text-xs px-2 mb-0">
                                <i class="material-icons text-xs" style="vertical-align: text-bottom;">info</i>
                                La référence est unique pour chaque billet et commence par "RES-".
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Résultat de recherche (Dynamique) -->
                <div id="searchResult" style="display: none;" class="mb-5">
                    <div id="resultCard" class="result-card bg-white">
                        <!-- Le contenu sera injecté ici par JS -->
                    </div>
                </div>

                <!-- État initial / Vide -->
                <div id="initialState" class="text-center py-5 mb-5">
                    <i class="material-icons text-slate-200 pulse-icon" style="font-size: 100px;">travel_explore</i>
                    <h4 class="mt-4 font-bold text-slate-900">En attente de saisie</h4>
                    <p class="text-slate-500 mx-auto" style="max-width: 400px;">Scannez un QR code ou entrez manuellement la référence pour voir les informations détaillées du passager.</p>
                </div>

                <!-- Erreur / Aucun résultat -->
                <div id="noResult" class="text-center py-5 mb-5" style="display: none;">
                    <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="material-icons" style="font-size: 48px;">search_off</i>
                    </div>
                    <h4 class="font-bold text-slate-900" id="noResultMessage">Réservation introuvable</h4>
                    <p class="text-slate-500">Vérifiez que la référence est correcte et essayez à nouveau.</p>
                </div>

                <!-- Historique des scans récents -->
                <!-- <div class="mt-5 mb-5 px-2">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="font-bold text-slate-900 m-0 d-flex align-items-center gap-2">
                            <i class="material-icons text-[#ff5a1f]">history</i>
                            Derniers passagers scannés
                        </h5>
                        <span class="badge bg-slate-100 text-slate-600 px-3 py-2 rounded-lg font-bold">Aujourd'hui</span>
                    </div>

                    <div class="recent-table-card">
                        <div class="table-responsive">
                            <table class="table table-hover table-premium m-0">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Passager</th>
                                        <th>Trajet / Gare</th>
                                        <th>Heure du scan</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($derniersScans as $s)
                                        <tr>
                                            <td data-label="Référence"><span class="ref-badge">{{ $s->reference }}</span></td>
                                            <td data-label="Passager">
                                                <div class="d-flex flex-column">
                                                    <span class="font-bold">{{ $s->passager_prenom }} {{ $s->passager_nom }}</span>
                                                    <span class="text-xs text-slate-400">{{ $s->passager_telephone }}</span>
                                                </div>
                                            </td>
                                            <td data-label="Trajet / Gare">
                                                <div class="d-flex flex-column">
                                                    <span class="font-bold">{{ $s->programme->point_depart }} → {{ $s->programme->point_arrive }}</span>
                                                    <span class="text-xs text-orange-500 uppercase font-bold tracking-tight">Siège n°{{ $s->seat_number }}</span>
                                                </div>
                                            </td>
                                            <td data-label="Heure du scan">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="material-icons text-slate-300 text-sm">schedule</i>
                                                    <span class="font-bold">{{ \Carbon\Carbon::parse($s->embarquement_scanned_at)->format('H:i') }}</span>
                                                </div>
                                            </td>
                                            <td data-label="Statut">
                                                <span class="status-badge-premium status-terminee">Scanné</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-slate-400">
                                                <i class="material-icons d-block mb-2" style="font-size: 32px;">notifications_none</i>
                                                Aucun scan enregistré pour le moment aujourd'hui.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div> -->

            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
            return;
        }
        
        // Masquer tout
        $('#initialState').hide();
        $('#searchResult').hide();
        $('#noResult').hide();
        
        // Afficher le loader
        $('#searchBtn').html('<span class="spinner-border spinner-border-sm me-2"></span>...').prop('disabled', true);
        
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
                var message = 'Réservation non trouvée';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                $('#noResultMessage').text(message);
                $('#noResult').show();
            },
            complete: function() {
                $('#searchBtn').html('<span>Rechercher</span><i class="material-icons">east</i>').prop('disabled', false);
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
                    <div class="p-4 bg-blue-50 rounded-3xl border border-blue-100">
                        <div class="section-title-premium" style="color: #2563eb;">
                            <i class="material-icons">verified</i>
                            DÉTAILS DU SCAN / EMBARQUEMENT
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="info-label">Heure du scan</div>
                                <div class="info-value text-blue-700">${r.embarquement.scanned_at}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Agent responsable</div>
                                <div class="info-value text-blue-700">${r.embarquement.agent}</div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-label">Véhicule assigné</div>
                                <div class="info-value text-blue-700">${r.embarquement.vehicule}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        var html = `
            <div class="result-header d-flex justify-content-between align-items-center">
                <div>
                    <div class="info-label text-white/60 mb-1">RÉFÉRENCE DE LA RÉSERVATION</div>
                    <h3 class="font-weight-bold mb-0" style="font-family: 'JetBrains Mono', monospace;">${r.reference}</h3>
                </div>
                <div class="text-right d-flex flex-column align-items-end">
                    <span class="status-badge-premium ${statusClass} mb-2">${statusText}</span>
                    <span class="text-white/60 text-xs">Réservé le ${r.created_at}</span>
                </div>
            </div>
            <div class="p-4 p-md-5">
                <div class="row g-4">
                    <!-- Infos Passager -->
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="section-title-premium">
                                <i class="material-icons">person</i>
                                Passager
                            </div>
                            <div class="mb-4">
                                <div class="info-label">Nom Complet</div>
                                <div class="info-value text-lg">${r.passager_prenom} ${r.passager_nom}</div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-label">Téléphone</div>
                                    <div class="info-value">${r.passager_telephone || 'N/A'}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Place n°</div>
                                    <div class="info-value">
                                        <span class="badge bg-orange-500 text-white rounded-lg px-2 py-1">${r.seat_number}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="info-label">Contact d'urgence</div>
                                <div class="info-value text-sm">${r.passager_urgence || 'N/A'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Infos Voyage -->
                    <div class="col-md-6">
                        <div class="info-group">
                            <div class="section-title-premium">
                                <i class="material-icons">directions_bus</i>
                                Trajet et Voyage
                            </div>
                            <div class="mb-4">
                                <div class="info-label">Parcours</div>
                                <div class="info-value text-lg">${r.trajet}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="info-label">Date</div>
                                    <div class="info-value">${r.date_voyage}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Départ</div>
                                    <div class="info-value">${r.heure_depart}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="info-label">Type</div>
                                    <div class="info-value">${r.is_aller_retour ? 'ALLER-RETOUR' : 'ALLER SIMPLE'}</div>
                                </div>
                                <div class="col-6">
                                    <div class="info-label">Montant</div>
                                    <div class="info-value text-success">${r.montant}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    ${embarquementHtml}
                </div>
            </div>
        `;
        
        $('#resultCard').html(html);
        $('#searchResult').show();
        
        // Scroll fluide vers le résultat
        $('html, body').animate({
            scrollTop: $("#searchResult").offset().top - 100
        }, 500);
    }
});
</script>
@endpush

