@extends('home.layouts.template')

@section('content')
    <!-- Hero Section -->
    <section class="contact-hero-section"
        style="background: linear-gradient(rgba(5, 30, 35, 0.85), rgba(5, 30, 35, 0.85)), url('{{ asset('assets/img/travel/destination-18.webp') }}') center/cover no-repeat;">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-lg-8 offset-lg-2 text-center" data-aos="fade-up">
                    <div class="hero-badge mb-3" style="background: rgba(233, 79, 27, 0.2); color: #e94f1b; border: 1px solid rgba(233, 79, 27, 0.3); padding: 5px 15px; display: inline-block; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase;">
                        Service de Convois Spéciaux
                    </div>
                    <h1 class="hero-title text-white mb-3" style="font-weight: 900; font-size: 2.8rem; letter-spacing: -1px;">
                        Voyagez en Groupe avec <span style="color: #e94f1b;">CAR225</span>
                    </h1>
                    <p class="hero-subtitle text-white-50 mb-4 mx-auto" style="max-width: 650px; font-size: 1.1rem;">
                        Réservez un car entier pour vos événements, sorties ou cérémonies. Choisissez entre nos compagnies partenaires officielles ou un transporteur particulier de confiance.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Convoi Section Content -->
    <section class="py-5" style="background-color: #F8F9FA;">
        <div class="container">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-4 rounded-3 mb-4" role="alert" style="background: #ecfdf5; border-left: 5px solid #10b981 !important;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-3" style="font-size: 24px;"></i>
                        <div>
                            <h5 class="alert-heading text-success mb-1" style="font-weight: 800;">Opération réussie !</h5>
                            <p class="mb-0 text-muted" style="font-size: 13px;">{{ session('success') }}</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm p-4 rounded-3 mb-4" role="alert" style="background: #fef2f2; border-left: 5px solid #ef4444 !important;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle text-danger me-3" style="font-size: 24px;"></i>
                        <div>
                            <h5 class="alert-heading text-danger mb-1" style="font-weight: 800;">Veuillez corriger les erreurs suivantes :</h5>
                            <ul class="mb-0 text-muted list-unstyled" style="font-size: 13px;">
                                @foreach ($errors->all() as $error)
                                    <li><i class="fas fa-dot-circle text-danger me-1 small"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Navigation Tabs (Premium Pills) -->
            <div class="d-flex justify-content-center mb-5">
                <div class="nav-pills-wrapper p-1 rounded-4 shadow-sm" style="background: white; border: 1px solid rgba(0,0,0,0.05); display: inline-flex;">
                    <button class="nav-link-btn active" id="tab-listings-btn" onclick="switchTab('listings')">
                        <i class="fas fa-list-ul me-2"></i> Convois Disponibles
                    </button>
                    <button class="nav-link-btn" id="tab-request-btn" onclick="switchTab('request')">
                        <i class="fas fa-bus-alt me-2"></i> Demander un Convoi
                    </button>
                    <button class="nav-link-btn" id="tab-become-btn" onclick="switchTab('become')">
                        <i class="fas fa-handshake me-2"></i> Devenir Transporteur
                    </button>
                </div>
            </div>

            <!-- Tab: Convois Disponibles -->
            <div class="tab-content-panel active" id="tab-listings">
                <!-- Filters -->
                <div class="d-flex flex-wrap gap-2 justify-content-center mb-4">
                    <a href="{{ route('home.convoi', ['type' => 'all']) }}" class="btn-filter {{ request('type') == 'all' || !request('type') ? 'active' : '' }}">
                        Tout afficher
                    </a>
                    <a href="{{ route('home.convoi', ['type' => 'compagnie']) }}" class="btn-filter {{ request('type') == 'compagnie' ? 'active' : '' }}">
                        <i class="fas fa-building me-1"></i> Par Compagnie
                    </a>
                    <a href="{{ route('home.convoi', ['type' => 'particulier']) }}" class="btn-filter {{ request('type') == 'particulier' ? 'active' : '' }}">
                        <i class="fas fa-user me-1"></i> Par Particulier
                    </a>
                </div>

                <div class="row g-4">
                    @forelse ($convois as $convoi)
                        <div class="col-md-6 col-lg-4">
                            <div class="card-convoi p-4 rounded-4 shadow-sm h-100 position-relative">
                                @php
                                    $isParticulier = (bool)$convoi->particulier_id;
                                    $transporter = $isParticulier ? $convoi->particulier : $convoi->compagnie;
                                    $bgBadge = $isParticulier ? 'background: #eff6ff; color: #2563eb;' : 'background: #fff7ed; color: #ea580c;';
                                    $iconType = $isParticulier ? 'fa-user' : 'fa-building';
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge-type" style="{{ $bgBadge }} font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 12px; text-transform: uppercase;">
                                        <i class="fas {{ $iconType }} me-1"></i> {{ $isParticulier ? 'Particulier' : 'Compagnie' }}
                                    </span>
                                    <span class="text-muted" style="font-size: 11px; font-weight: 700;">Ref: {{ $convoi->reference }}</span>
                                </div>

                                <h5 class="fw-bold mb-2 text-dark">
                                    {{ $convoi->lieu_depart }} <i class="fas fa-arrow-right mx-1 text-muted small"></i> {{ $convoi->lieu_retour }}
                                </h5>

                                <div class="convoi-detail-row text-muted mb-2">
                                    <i class="fas fa-calendar-alt text-orange me-2"></i>
                                    Départ : <strong>{{ \Carbon\Carbon::parse($convoi->date_depart)->format('d/m/Y') }}</strong> à <strong>{{ substr($convoi->heure_depart, 0, 5) }}</strong>
                                </div>
                                @if($convoi->date_retour)
                                    <div class="convoi-detail-row text-muted mb-2">
                                        <i class="fas fa-history text-orange me-2"></i>
                                        Retour : <strong>{{ \Carbon\Carbon::parse($convoi->date_retour)->format('d/m/Y') }}</strong> à <strong>{{ substr($convoi->heure_retour, 0, 5) }}</strong>
                                    </div>
                                @endif

                                <div class="convoi-detail-row text-muted mb-3">
                                    <i class="fas fa-users text-orange me-2"></i>
                                    Places réservées : <strong>{{ $convoi->nombre_personnes }} places</strong>
                                </div>

                                <hr class="my-3 opacity-10">

                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-holder me-2">
                                            @if($isParticulier && $convoi->particulier && $convoi->particulier->photo_proprietaire)
                                                <img src="{{ $convoi->particulier->photo_proprietaire_url }}" alt="avatar" class="rounded-full shadow-sm" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-full d-flex align-items-center justify-content-center bg-gray-200" style="width: 32px; height: 32px; font-weight: 800; font-size: 12px; color: #555;">
                                                    {{ substr($convoi->transporter_name, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="mb-0 text-dark font-black" style="font-size: 12px;">{{ $convoi->transporter_name }}</p>
                                            <p class="mb-0 text-muted" style="font-size: 10px;">Prestataire</p>
                                        </div>
                                    </div>

                                    @php
                                        $sm = [
                                            'en_attente' => ['En attente',  'bg-warning text-dark'],
                                            'valide'     => ['Validé',      'bg-info text-white'],
                                            'refuse'     => ['Refusé',      'bg-danger text-white'],
                                            'paye'       => ['Payé',        'bg-success text-white'],
                                            'en_cours'   => ['En cours',    'bg-primary text-white'],
                                            'termine'    => ['Terminé',     'bg-secondary text-white'],
                                            'annule'     => ['Annulé',      'bg-danger text-white'],
                                        ];
                                        [$slabel, $sclass] = $sm[$convoi->statut] ?? [$convoi->statut, 'bg-secondary text-white'];
                                    @endphp
                                    <span class="badge {{ $sclass }} text-uppercase" style="font-size: 9px; font-weight: 800; padding: 4px 8px; border-radius: 6px;">
                                        {{ $slabel }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <div class="mb-3 text-muted">
                                <i class="fas fa-bus-alt" style="font-size: 48px;"></i>
                            </div>
                            <p class="text-muted fw-bold">Aucun convoi disponible sur la plateforme pour le moment.</p>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $convois->links() }}
                </div>
            </div>

            <!-- Tab: Demander un convoi -->
            <div class="tab-content-panel" id="tab-request">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="form-wrapper p-4 p-md-5 rounded-4 shadow-sm bg-white position-relative overflow-hidden">
                            @if (!auth()->check())
                                <!-- Glassmorphic overlay for guest users -->
                                <div class="guest-overlay d-flex flex-column align-items-center justify-content-center text-center p-4">
                                    <div class="overlay-card p-4 rounded-4 shadow-lg bg-white/95 backdrop-blur-md" style="max-width: 450px; border: 1px solid rgba(255,255,255,0.4);">
                                        <div class="w-16 h-16 bg-orange/10 text-orange rounded-full d-flex align-items-center justify-content-center mx-auto mb-3" style="width:64px; height:64px; font-size:28px;">
                                            <i class="fas fa-lock"></i>
                                        </div>
                                        <h4 class="fw-bold mb-2">Connexion requise</h4>
                                        <p class="text-muted mb-4" style="font-size: 14px;">
                                            Vous devez vous connecter à votre compte utilisateur CAR225 pour soumettre une demande de convoi et suivre son avancement.
                                        </p>
                                        <a href="{{ route('login') }}" class="btn btn-orange-premium w-100 py-3 rounded-3 text-uppercase font-black tracking-wider text-xs">
                                            Se connecter maintenant
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <h3 class="fw-bold mb-1">Formuler une <span style="color: #e94f1b;">Demande de Convoi</span></h3>
                            <p class="text-muted mb-4" style="font-size: 14px;">
                                Renseignez les informations de voyage. Nous transmettrons directement votre demande au prestataire choisi.
                            </p>

                            <form action="{{ route('user.convoi.store') }}" method="POST" id="convoiForm">
                                @csrf

                                <!-- Type de transporteur -->
                                <div class="mb-4">
                                    <label class="form-label-custom">TYPE DE PRESTATAIRE <span class="text-danger">*</span></label>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="provider-radio-card" id="card-prov-compagnie" onclick="selectProviderType('compagnie')">
                                                <input type="radio" name="type_transporteur" id="prov-compagnie" value="compagnie" checked style="display:none;">
                                                <div class="d-flex align-items-center">
                                                    <div class="radio-icon me-3">
                                                        <i class="fas fa-building"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-bold title-radio">Compagnie Officielle</p>
                                                        <p class="mb-0 desc-radio">Bus et gares certifiés</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="provider-radio-card" id="card-prov-particulier" onclick="selectProviderType('particulier')">
                                                <input type="radio" name="type_transporteur" id="prov-particulier" value="particulier" style="display:none;">
                                                <div class="d-flex align-items-center">
                                                    <div class="radio-icon me-3">
                                                        <i class="fas fa-user-friends"></i>
                                                    </div>
                                                    <div>
                                                        <p class="mb-0 fw-bold title-radio">Transporteur Particulier</p>
                                                        <p class="mb-0 desc-radio">Particulier agréé CAR225</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Compagnie Select Container -->
                                 <div id="compagnie-select-group">
                                     <div class="mb-4">
                                         <label class="form-label-custom">COMPAGNIE <span class="text-danger">*</span></label>
                                         
                                         <!-- Toggle Button for Compagnie -->
                                         <button type="button" id="btnShowCompagnies" class="btn btn-outline-secondary w-100 py-3 rounded-3 fw-bold text-sm d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#compagnieSelectModal">
                                             <i class="fas fa-building me-2 text-orange"></i> Choisir une compagnie officielle
                                         </button>
                                         
                                         <!-- Selected Badge for Compagnie (Initially Hidden) -->
                                         <div id="selectedCompagnieBadge" class="d-none mt-2 p-3 bg-light border border-gray-100 rounded-3 d-flex align-items-center justify-content-between">
                                             <div class="d-flex align-items-center text-start">
                                                 <div class="logo-circle me-3 overflow-hidden rounded-circle bg-gray-50 flex align-items-center justify-content-center border" style="width: 44px; height: 44px; border-radius: 50%;">
                                                     <img id="selectedCompPhoto" src="" alt="Compagnie" class="w-100 h-100 object-cover" style="border-radius: 50%;">
                                                 </div>
                                                 <div>
                                                     <p class="mb-0 fw-bold text-dark text-sm" id="selectedCompName" style="font-size: 13px;">Nom Compagnie</p>
                                                     <p class="mb-0 text-muted" style="font-size: 10px;" id="selectedCompSigle">Sigle</p>
                                                 </div>
                                             </div>
                                             <div>
                                                 <button type="button" class="btn btn-sm btn-link text-danger fw-bold text-decoration-none" style="font-size: 11px;" onclick="clearCompagnieSelection()">
                                                     Désélectionner
                                                 </button>
                                             </div>
                                         </div>

                                         <input type="hidden" name="compagnie_id" id="compagnieSelect">
                                     </div>
                                     
                                     <div class="mb-3">
                                        <label class="form-label-custom">GARE LA PLUS PROCHE <span class="text-danger">*</span></label>
                                        <select id="gareSelect" name="gare_id" class="form-select form-input-premium" disabled>
                                            <option value="">Choisir d'abord une compagnie</option>
                                        </select>
                                        <span class="text-muted small font-semibold mt-1 block"><i class="fas fa-info-circle me-1"></i> Cette gare traitera votre demande.</span>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label-custom">ITINÉRAIRE <span class="text-muted">(optionnel)</span></label>
                                        <select id="itineraireSelect" name="itineraire_id" class="form-select form-input-premium" disabled>
                                            <option value="">Choisir d'abord une compagnie</option>
                                        </select>
                                    </div>
                                </div>
                                    
                                <!-- Particulier Select Container -->
                                <div id="particulier-select-group" class="d-none">
                                    <div class="mb-3">
                                        <label class="form-label-custom">TRANSPORTEUR PARTICULIER <span class="text-muted">(Optionnel)</span></label>
                                        
                                        <!-- Toggle Button -->
                                        <button type="button" id="btnShowParticuliers" class="btn btn-outline-secondary w-100 py-3 rounded-3 fw-bold text-sm d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#particulierSelectModal">
                                            <i class="fas fa-user-plus me-2 text-orange"></i> Choisir un transporteur particulier
                                        </button>
                                        
                                        <!-- Selected Badge (Initially Hidden) -->
                                        <div id="selectedParticulierBadge" class="d-none mt-2 p-3 bg-light border border-gray-100 rounded-3 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center text-start">
                                                <div class="logo-circle me-3 overflow-hidden rounded-circle bg-gray-50 flex align-items-center justify-content-center border" style="width: 44px; height: 44px; border-radius: 50%;">
                                                    <img id="selectedPartPhoto" src="" alt="Chauffeur" class="w-100 h-100 object-cover" style="border-radius: 50%;">
                                                </div>
                                                <div>
                                                    <p class="mb-0 fw-bold text-dark text-sm" id="selectedPartName" style="font-size: 13px;">Nom</p>
                                                    <p class="mb-0 text-muted" style="font-size: 10px;" id="selectedPartCarInfo">Capacité & Plaque</p>
                                                </div>
                                            </div>
                                            <div class="d-flex gap-2 align-items-center">
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1 rounded-3 text-xs" style="font-size: 10px;" id="btnDetailsSelectedPart">
                                                    Détails
                                                </button>
                                                <button type="button" class="btn btn-sm btn-link text-danger fw-bold text-decoration-none" style="font-size: 11px;" onclick="clearParticulierSelection()">
                                                    Désélectionner
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="particulier_id" id="particulierSelect">
                                </div>

                                <!-- Common Fields -->
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">LIEU DE DÉPART <span class="text-danger">*</span></label>
                                            <input type="text" id="lieuDepart" name="lieu_depart" class="form-control form-input-premium" placeholder="Ex: Abidjan Cocody">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">LIEU D'ARRIVÉE <span class="text-danger">*</span></label>
                                            <input type="text" id="lieuArrivee" name="lieu_retour" class="form-control form-input-premium" placeholder="Ex: Yamoussoukro Fondation">
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label-custom">DATE ALLER <span class="text-danger">*</span></label>
                                            <input type="date" name="date_depart" id="date_depart" min="{{ date('Y-m-d') }}" class="form-control form-input-premium">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label-custom">HEURE ALLER <span class="text-danger">*</span></label>
                                            <input type="time" name="heure_depart" id="heure_depart" class="form-control form-input-premium">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label-custom">DATE RETOUR <span class="text-muted">(optionnel)</span></label>
                                            <input type="date" name="date_retour" id="date_retour" min="{{ date('Y-m-d') }}" class="form-control form-input-premium">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-md-3">
                                        <div class="mb-3">
                                            <label class="form-label-custom">HEURE RETOUR <span class="text-muted">(optionnel)</span></label>
                                            <input type="time" name="heure_retour" id="heure_retour" class="form-control form-input-premium">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label-custom">NOMBRE DE PLACES ATTENDUES <span class="text-danger">*</span></label>
                                    <input type="number" name="nombre_personnes" id="nombre_personnes" min="10" value="10" class="form-control form-input-premium">
                                    <span class="text-muted small font-semibold mt-1 block"><i class="fas fa-info-circle me-1"></i> Le minimum requis pour un convoi est de 10 personnes.</span>
                                </div>

                                <button type="submit" class="btn btn-orange-premium w-100 py-3 rounded-3 text-uppercase font-black tracking-wider text-xs">
                                    <i class="fas fa-paper-plane me-2"></i> Soumettre ma demande de convoi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab: Devenir transporteur particulier -->
            <div class="tab-content-panel" id="tab-become">
                <div class="row">
                    <div class="col-lg-8 offset-lg-2">
                        <div class="form-wrapper p-4 p-md-5 rounded-4 shadow-sm bg-white">
                            <h3 class="fw-bold mb-1">Devenir <span style="color: #e94f1b;">Transporteur Particulier</span></h3>
                            <p class="text-muted mb-4" style="font-size: 14px;">
                                Vous disposez d'un car de transport et souhaitez proposer vos services de convoi ? Soumettez votre demande. Après validation par l'administration, vous recevrez vos identifiants par email et SMS.
                            </p>

                            <form action="{{ route('home.convoi.particulier.register') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <h5 class="fw-bold border-bottom pb-2 mb-3 text-dark font-black text-sm uppercase tracking-wide">
                                    <i class="fas fa-user text-orange me-2"></i> Informations du propriétaire
                                </h5>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">NOM <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control form-input-premium" placeholder="Nom de famille" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">PRÉNOMS <span class="text-danger">*</span></label>
                                            <input type="text" name="prenom" class="form-control form-input-premium" placeholder="Prénoms" value="{{ old('prenom') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">EMAIL <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control form-input-premium" placeholder="adresse@exemple.com" value="{{ old('email') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">CONTACT TÉLÉPHONIQUE <span class="text-danger">*</span></label>
                                            <input type="tel" name="contact" class="form-control form-input-premium" placeholder="Ex: 0707070707" value="{{ old('contact') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label-custom">PHOTO D'IDENTITÉ DU PROPRIÉTAIRE <span class="text-danger">*</span></label>
                                    <input type="file" name="photo_proprietaire" class="form-control form-input-premium" accept="image/*" required>
                                </div>

                                <h5 class="fw-bold border-bottom pb-2 mb-3 mt-4 text-dark font-black text-sm uppercase tracking-wide">
                                    <i class="fas fa-bus text-orange me-2"></i> Informations du véhicule
                                </h5>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">NOMBRE DE PLACES DU CAR <span class="text-danger">* (Min. 10)</span></label>
                                            <input type="number" name="nombre_place_car" min="10" class="form-control form-input-premium" placeholder="Ex: 32" value="{{ old('nombre_place_car', 10) }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">IMMATRICULATION DU VÉHICULE <span class="text-danger">*</span></label>
                                            <input type="text" name="immatriculation" class="form-control form-input-premium" placeholder="Ex: 1234AB01" value="{{ old('immatriculation') }}" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">DATE DE MISE EN SERVICE <span class="text-danger">*</span></label>
                                            <input type="date" name="date_mise_service" id="date_mise_service" max="{{ date('Y-m-d') }}" class="form-control form-input-premium" value="{{ old('date_mise_service') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 d-flex align-items-center">
                                        <div id="calculated_age_container" class="hidden w-100 p-3 rounded-3" style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; font-weight: 700; font-size: 13px;">
                                            <span id="calculated_age">Âge estimé : --</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label-custom">PHOTO COMPLÈTE DU CAR <span class="text-danger">*</span></label>
                                            <input type="file" name="photo_complete_car" class="form-control form-input-premium" accept="image/*" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label-custom">PHOTO AVANT <span class="text-danger">*</span></label>
                                            <input type="file" name="photo_avant_car" class="form-control form-input-premium" accept="image/*" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label-custom">PHOTO ARRIÈRE <span class="text-danger">*</span></label>
                                            <input type="file" name="photo_arriere_car" class="form-control form-input-premium" accept="image/*" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">CARTE GRISE (Photo ou PDF) <span class="text-danger">*</span></label>
                                            <input type="file" name="carte_grise" class="form-control form-input-premium" accept="image/*,application/pdf" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-3">
                                            <label class="form-label-custom">VISITE TECHNIQUE (Photo ou PDF) <span class="text-danger">*</span></label>
                                            <input type="file" name="visite_technique" class="form-control form-input-premium" accept="image/*,application/pdf" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-orange-premium w-100 py-3 rounded-3 text-uppercase font-black tracking-wider text-xs mt-4">
                                    <i class="fas fa-check-circle me-2"></i> Soumettre ma demande d'inscription
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- Custom Styling & Tabs JS -->
    <style>
        .nav-link-btn {
            background: transparent;
            border: none;
            padding: 10px 24px;
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 12px;
            color: #6b7280;
            transition: all 0.25s ease;
        }
        .nav-link-btn.active {
            background: #e94f1b;
            color: white !important;
            box-shadow: 0 4px 15px rgba(233, 79, 27, 0.2);
        }
        .nav-link-btn:hover:not(.active) {
            color: #1a1d1f;
            background: #f4f5f6;
        }
        .tab-content-panel {
            display: none;
        }
        .tab-content-panel.active {
            display: block;
            animation: fadeIn 0.4s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-filter {
            background: white;
            color: #4b5563;
            border: 1px solid rgba(0,0,0,0.08);
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-filter:hover {
            background: #f9fafb;
            color: #e94f1b;
        }
        .btn-filter.active {
            background: #e94f1b;
            color: white !important;
            border-color: #e94f1b;
            box-shadow: 0 4px 10px rgba(233, 79, 27, 0.15);
        }
        .card-convoi {
            background: white;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }
        .card-convoi:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
            border-color: rgba(233, 79, 27, 0.15);
        }
        .text-orange {
            color: #e94f1b;
        }
        .convoi-detail-row {
            font-size: 13px;
            display: flex;
            align-items: center;
        }
        .convoi-detail-row i {
            width: 18px;
        }
        .form-label-custom {
            font-size: 11px;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
            display: block;
        }
        .form-input-premium {
            border: 1px solid rgba(0,0,0,0.08);
            background: #f9fafb;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            outline: none;
            transition: all 0.2s ease;
        }
        .form-input-premium:focus {
            border-color: #e94f1b;
            background: white;
            box-shadow: 0 0 0 3px rgba(233, 79, 27, 0.15);
        }
        .provider-radio-card {
            background: #f9fafb;
            border: 2px solid rgba(0,0,0,0.05);
            border-radius: 16px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .provider-radio-card:hover {
            border-color: rgba(233, 79, 27, 0.3);
            background: #fffdfb;
        }
        .provider-radio-card.active {
            border-color: #e94f1b;
            background: #fffaf7;
            box-shadow: 0 4px 15px rgba(233, 79, 27, 0.05);
        }
        .provider-radio-card.active .radio-icon {
            background: #e94f1b;
            color: white;
        }
        .radio-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(0,0,0,0.04);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #4b5563;
            transition: all 0.2s ease;
        }
        .title-radio {
            font-size: 13px;
            font-weight: 800;
            color: #1f2937;
        }
        .desc-radio {
            font-size: 11px;
            color: #6b7280;
            font-weight: 600;
        }
        .btn-orange-premium {
            background: #e94f1b;
            color: white;
            font-weight: 800;
            transition: all 0.25s ease;
            border: none;
            box-shadow: 0 4px 15px rgba(233, 79, 27, 0.2);
        }
        .btn-orange-premium:hover {
            background: #d44518;
            color: white;
            box-shadow: 0 6px 20px rgba(233, 79, 27, 0.3);
        }
        .guest-overlay {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(248, 249, 250, 0.7);
            backdrop-filter: blur(8px);
            z-index: 100;
            border-radius: 16px;
        }
        .hidden {
            display: none !important;
        }
        /* Provider select cards styling */
        .provider-select-card {
            background: white;
            border: 2px solid rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .provider-select-card:hover {
            border-color: rgba(233, 79, 27, 0.2);
            background: #fffdfb;
            transform: translateY(-2px);
        }
        .provider-select-card.active {
            border-color: #e94f1b;
            background: #fffaf7;
            box-shadow: 0 8px 25px rgba(233, 79, 27, 0.08) !important;
        }
        .provider-select-card.active .btn-orange-select {
            background: #e94f1b !important;
            color: white !important;
            border-color: #e94f1b !important;
        }
        .btn-orange-select {
            background: rgba(0,0,0,0.04);
            color: #4b5563;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }
        .btn-orange-select:hover {
            background: #e94f1b;
            color: white;
        }
        .bg-success-light {
            background: #ecfdf5;
            color: #065f46;
        }
    </style>

    <script>
        function switchTab(tabId) {
            // Hide all tab panels
            document.querySelectorAll('.tab-content-panel').forEach(panel => {
                panel.classList.remove('active');
            });
            // Deactivate all tab buttons
            document.querySelectorAll('.nav-link-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected panel
            document.getElementById('tab-' + tabId).classList.add('active');
            // Activate selected button
            document.getElementById('tab-' + tabId + '-btn').classList.add('active');

            // Save tab state in URL hash
            window.location.hash = tabId;
        }

        function selectProviderType(type) {
            // Uncheck/Check inputs
            document.getElementById('prov-compagnie').checked = (type === 'compagnie');
            document.getElementById('prov-particulier').checked = (type === 'particulier');

            // Toggle active classes on radio cards
            document.getElementById('card-prov-compagnie').classList.toggle('active', type === 'compagnie');
            document.getElementById('card-prov-particulier').classList.toggle('active', type === 'particulier');

            // Show/Hide dropdown groups
            if (type === 'compagnie') {
                document.getElementById('compagnie-select-group').classList.remove('d-none');
                document.getElementById('particulier-select-group').classList.add('d-none');
                document.getElementById('compagnieSelect').required = true;
                document.getElementById('gareSelect').required = true;
                document.getElementById('particulierSelect').required = false;
            } else {
                document.getElementById('compagnie-select-group').classList.add('d-none');
                document.getElementById('particulier-select-group').classList.remove('d-none');
                document.getElementById('compagnieSelect').required = false;
                document.getElementById('gareSelect').required = false;
                document.getElementById('particulierSelect').required = false; // Particulier selection is now optional
            }
        }

        function selectCompagnieCard(id) {
            // Remove active classes
            document.querySelectorAll('#compagnieCardsGroup .provider-select-card').forEach(card => {
                card.classList.remove('active');
            });
            // Add active class
            const activeCard = document.getElementById('card-compagnie-' + id);
            if (activeCard) {
                activeCard.classList.add('active');
                
                // Get data attributes for badge populating
                const name = activeCard.getAttribute('data-name');
                const logo = activeCard.getAttribute('data-logo');
                const sigle = activeCard.getAttribute('data-sigle');

                // Fill selection badge
                document.getElementById('selectedCompPhoto').src = logo;
                document.getElementById('selectedCompName').textContent = name;
                document.getElementById('selectedCompSigle').textContent = sigle;

                // Show selected badge and hide the selection button
                document.getElementById('selectedCompagnieBadge').classList.remove('d-none');
                document.getElementById('btnShowCompagnies').classList.add('d-none');
            }

            // Set hidden value
            const compSelect = document.getElementById('compagnieSelect');
            if (compSelect) {
                compSelect.value = id;
                // Dispatch change event to trigger existing AJAX loaders for gares and itineraries!
                compSelect.dispatchEvent(new Event('change'));
            }

            // Close the select modal programmatically
            const modalEl = document.getElementById('compagnieSelectModal');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();
            }
        }

        function clearCompagnieSelection() {
            // Remove active classes
            document.querySelectorAll('#compagnieCardsGroup .provider-select-card').forEach(card => {
                card.classList.remove('active');
            });

            // Reset hidden input
            const compSelect = document.getElementById('compagnieSelect');
            if (compSelect) {
                compSelect.value = '';
                // Dispatch change event to trigger reset of gares & itineraries
                compSelect.dispatchEvent(new Event('change'));
            }

            // Hide badge and show the select button
            document.getElementById('selectedCompagnieBadge').classList.add('d-none');
            document.getElementById('btnShowCompagnies').classList.remove('d-none');
        }

        function selectParticulierCard(id) {
            // Remove active classes
            document.querySelectorAll('#particulierCardsGroup .provider-select-card').forEach(card => {
                card.classList.remove('active');
            });
            // Add active class
            const activeCard = document.getElementById('card-particulier-' + id);
            if (activeCard) {
                activeCard.classList.add('active');
                
                // Get data attributes for badge populating
                const name = activeCard.getAttribute('data-name');
                const photo = activeCard.getAttribute('data-photo');
                const places = activeCard.getAttribute('data-places');
                const immat = activeCard.getAttribute('data-immat');

                // Fill selection badge
                document.getElementById('selectedPartPhoto').src = photo;
                document.getElementById('selectedPartName').textContent = name;
                document.getElementById('selectedPartCarInfo').textContent = places + ' places · ' + immat;
                
                // Set click action for Details button on the badge
                document.getElementById('btnDetailsSelectedPart').onclick = function() {
                    showParticulierInfoModalFromCard(id);
                };

                // Show selected badge and hide the selection button
                document.getElementById('selectedParticulierBadge').classList.remove('d-none');
                document.getElementById('btnShowParticuliers').classList.add('d-none');
            }

            // Set hidden value
            const partSelect = document.getElementById('particulierSelect');
            if (partSelect) {
                partSelect.value = id;
            }

            // Close the select modal programmatically
            const modalEl = document.getElementById('particulierSelectModal');
            if (modalEl) {
                const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modalInstance.hide();
            }
        }

        function clearParticulierSelection() {
            // Remove active classes
            document.querySelectorAll('#particulierCardsGroup .provider-select-card').forEach(card => {
                card.classList.remove('active');
            });

            // Reset hidden input
            const partSelect = document.getElementById('particulierSelect');
            if (partSelect) {
                partSelect.value = '';
            }

            // Hide badge and show the select button
            document.getElementById('selectedParticulierBadge').classList.add('d-none');
            document.getElementById('btnShowParticuliers').classList.remove('d-none');
        }

        function showParticulierInfoModalFromCard(id) {
            const card = document.getElementById('card-particulier-' + id);
            if (card) {
                const dataJson = card.getAttribute('data-json');
                if (dataJson) {
                    showParticulierInfoModal(JSON.parse(dataJson));
                }
            }
        }

        function showParticulierInfoModal(data) {
            document.getElementById('modalPartPhotoProprietaire').src = data.photo_proprietaire;
            document.getElementById('modalPartName').textContent = data.nom;
            document.getElementById('modalPartImmatriculation').textContent = data.immatriculation;
            document.getElementById('modalPartPlaces').textContent = data.places + ' places';
            document.getElementById('modalPartDateService').textContent = data.date_service;
            document.getElementById('modalPartPhotoComplete').src = data.photo_complete;
            document.getElementById('modalPartPhotoAvant').src = data.photo_avant;
            document.getElementById('modalPartPhotoArriere').src = data.photo_arriere;

            // Trigger Bootstrap Modal
            const modalEl = document.getElementById('particulierDetailsModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }

        // Initialize state on load
        document.addEventListener('DOMContentLoaded', function() {
            // Read tab from hash if exists
            const hash = window.location.hash.replace('#', '');
            if (hash === 'request' || hash === 'become' || hash === 'listings') {
                switchTab(hash);
            }

            // Restore provider type and selections
            const oldType = "{{ old('type_transporteur', 'compagnie') }}";
            selectProviderType(oldType);

            const oldCompId = "{{ old('compagnie_id') }}";
            if (oldCompId) {
                selectCompagnieCard(oldCompId);
            }

            const oldPartId = "{{ old('particulier_id') }}";
            if (oldPartId) {
                selectParticulierCard(oldPartId);
            }

            // Real-time Vehicle Age calculation in JS
            const dateMiseService = document.getElementById('date_mise_service');
            if (dateMiseService) {
                dateMiseService.addEventListener('change', function() {
                    const inputDate = new Date(this.value);
                    const today = new Date();
                    if (isNaN(inputDate.getTime())) return;

                    let years = today.getFullYear() - inputDate.getFullYear();
                    let months = today.getMonth() - inputDate.getMonth();
                    let days = today.getDate() - inputDate.getDate();

                    if (days < 0) {
                        months--;
                        const prevMonth = new Date(today.getFullYear(), today.getMonth(), 0);
                        days += prevMonth.getDate();
                    }
                    if (months < 0) {
                        years--;
                        months += 12;
                    }

                    let text = "";
                    if (years > 0) text += years + (years > 1 ? " ans " : " an ");
                    if (months > 0) text += months + " mois";

                    if (text === "") {
                        text = "Moins d'un mois";
                    }

                    document.getElementById('calculated_age').textContent = "Âge estimé du car : " + text;
                    document.getElementById('calculated_age_container').classList.remove('hidden');
                });
            }
        });

        // AJAX handlers for Company stations and itineraries
        const compagnieSelect = document.getElementById('compagnieSelect');
        const gareSelect = document.getElementById('gareSelect');
        const itineraireSelect = document.getElementById('itineraireSelect');

        if (compagnieSelect) {
            compagnieSelect.addEventListener('change', async function() {
                const compagnieId = this.value;
                if (!compagnieId) {
                    gareSelect.innerHTML = '<option value="">Choisir d\'abord une compagnie</option>';
                    gareSelect.disabled = true;
                    itineraireSelect.innerHTML = '<option value="">Choisir d\'abord une compagnie</option>';
                    itineraireSelect.disabled = true;
                    return;
                }

                gareSelect.innerHTML = '<option value="">Chargement...</option>';
                gareSelect.disabled = true;
                itineraireSelect.innerHTML = '<option value="">Chargement...</option>';
                itineraireSelect.disabled = true;

                try {
                    // Load Gares
                    const resGares = await fetch(`/user/convoi/compagnie/${compagnieId}/gares`);
                    const dataGares = await resGares.json();
                    let optGares = '<option value="">Choisir une gare...</option>';
                    (dataGares.gares || []).forEach(g => {
                        optGares += `<option value="${g.id}">${g.nom_gare} (${g.ville || ''})</option>`;
                    });
                    gareSelect.innerHTML = optGares;
                    gareSelect.disabled = false;

                    // Load Itineraires
                    const resItin = await fetch(`/user/convoi/compagnie/${compagnieId}/itineraires`);
                    const dataItin = await resItin.json();
                    let optItin = '<option value="">— Saisir manuellement —</option>';
                    (dataItin.itineraires || []).forEach(it => {
                        optItin += `<option value="${it.id}" data-depart="${it.point_depart}" data-arrive="${it.point_arrive}">` +
                                   `${it.point_depart} → ${it.point_arrive}</option>`;
                    });
                    itineraireSelect.innerHTML = optItin;
                    itineraireSelect.disabled = false;

                } catch (error) {
                    console.error("Erreur de chargement AJAX:", error);
                    gareSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                    itineraireSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                }
            });
        }

        // Fill departure / arrival when itinerary is selected
        if (itineraireSelect) {
            itineraireSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const dep = opt.getAttribute('data-depart');
                const arr = opt.getAttribute('data-arrive');
                const lieuDep = document.getElementById('lieuDepart');
                const lieuArr = document.getElementById('lieuArrivee');

                if (dep && arr) {
                    lieuDep.value = dep;
                    lieuArr.value = arr;
                    lieuDep.readOnly = true;
                    lieuArr.readOnly = true;
                    lieuDep.style.background = '#e5e7eb';
                    lieuArr.style.background = '#e5e7eb';
                } else {
                    lieuDep.readOnly = false;
                    lieuArr.readOnly = false;
                    lieuDep.style.background = '#f9fafb';
                    lieuArr.style.background = '#f9fafb';
                }
            });
        }
    </script>

    <!-- Modal Selection Compagnie -->
    <div class="modal fade" id="compagnieSelectModal" tabindex="-1" aria-labelledby="compagnieSelectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark" id="compagnieSelectModalLabel" style="font-size: 16px; font-weight: 800;">
                        <i class="fas fa-building text-orange me-2"></i> Choisir une compagnie officielle
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-muted text-xs mb-4" style="font-size: 12px;">
                        Sélectionnez une compagnie de transport officielle ci-dessous pour lui soumettre votre demande de convoi.
                    </p>
                    <div class="row g-3" id="compagnieCardsGroup">
                        @foreach ($compagnies as $compagnie)
                            <div class="col-sm-6">
                                <div class="provider-select-card p-3 rounded-4 shadow-sm text-center position-relative border" 
                                    id="card-compagnie-{{ $compagnie->id }}" 
                                    onclick="selectCompagnieCard({{ $compagnie->id }})"
                                    data-name="{{ $compagnie->name }}"
                                    data-logo="{{ $compagnie->path_logo ? asset('storage/' . $compagnie->path_logo) : asset('assetsPoster/assets/images/logo_car225.png') }}"
                                    data-sigle="{{ $compagnie->sigle ?: 'Compagnie' }}"
                                    style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="logo-circle mx-auto mb-2 d-flex align-items-center justify-content-center bg-gray-50 border border-gray-100 rounded-circle" style="width: 56px; height: 56px; overflow: hidden; border-radius: 50%;">
                                        <img src="{{ $compagnie->path_logo ? asset('storage/' . $compagnie->path_logo) : asset('assetsPoster/assets/images/logo_car225.png') }}" alt="{{ $compagnie->name }}" class="w-100 h-100 object-cover" style="border-radius: 50%;">
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark text-sm truncate" style="font-size: 13px;">{{ $compagnie->name }}</h6>
                                    <p class="mb-2 text-muted" style="font-size: 10px;">{{ $compagnie->sigle ?: 'Compagnie' }}</p>
                                    
                                    <button type="button" class="btn btn-sm btn-orange-select px-3 py-1 rounded-3 text-xs w-100" style="font-size: 10px; font-weight: 800;">
                                        Sélectionner
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-3 text-xs px-4 py-2" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Selection Particulier -->
    <div class="modal fade" id="particulierSelectModal" tabindex="-1" aria-labelledby="particulierSelectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold text-dark" id="particulierSelectModalLabel" style="font-size: 16px; font-weight: 800;">
                        <i class="fas fa-user-friends text-orange me-2"></i> Choisir un transporteur particulier
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <p class="text-muted text-xs mb-4" style="font-size: 12px;">
                        Sélectionnez un transporteur particulier agréé ci-dessous pour lui envoyer directement votre demande. Si aucun n'est sélectionné, la demande sera envoyée à l'ensemble des transporteurs.
                    </p>
                    <div class="row g-3" id="particulierCardsGroup">
                        @forelse($particuliers as $particulier)
                            <div class="col-sm-6">
                                <div class="provider-select-card p-3 rounded-4 shadow-sm text-center position-relative border" 
                                    id="card-particulier-{{ $particulier->id }}" 
                                    onclick="selectParticulierCard({{ $particulier->id }})"
                                    data-name="{{ $particulier->full_name }}"
                                    data-photo="{{ $particulier->photo_proprietaire_url }}"
                                    data-places="{{ $particulier->nombre_place_car }}"
                                    data-immat="{{ $particulier->immatriculation }}"
                                    data-json="{{ json_encode([
                                        'nom' => $particulier->full_name,
                                        'contact' => $particulier->contact,
                                        'email' => $particulier->email,
                                        'places' => $particulier->nombre_place_car,
                                        'immatriculation' => $particulier->immatriculation,
                                        'date_service' => $particulier->date_mise_service->format('d/m/Y'),
                                        'photo_proprietaire' => $particulier->photo_proprietaire_url,
                                        'photo_complete' => $particulier->photo_complete_car_url,
                                        'photo_avant' => $particulier->photo_avant_car_url,
                                        'photo_arriere' => $particulier->photo_arriere_car_url,
                                    ]) }}" style="cursor: pointer; transition: all 0.2s ease;">
                                    <div class="logo-circle mx-auto mb-2 d-flex align-items-center justify-content-center bg-gray-50 border border-gray-100 rounded-circle" style="width: 56px; height: 56px; overflow: hidden; border-radius: 50%;">
                                        <img src="{{ $particulier->photo_proprietaire_url }}" alt="{{ $particulier->full_name }}" class="w-100 h-100 object-cover" style="border-radius: 50%;">
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark text-sm truncate" style="font-size: 13px;">{{ $particulier->full_name }}</h6>
                                    <p class="mb-2 text-muted" style="font-size: 10px;">{{ $particulier->nombre_place_car }} places · {{ $particulier->immatriculation }}</p>
                                    
                                    <div class="d-flex gap-1.5 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-orange-select px-3 py-1 rounded-3 text-xs flex-grow-1" style="font-size: 10px; font-weight: 800;">
                                            Sélectionner
                                        </button>
                                        <button type="button" class="btn btn-sm btn-light px-2 py-1 rounded-3 text-xs" style="font-size: 10px; font-weight: 700; border: 1px solid rgba(0,0,0,0.08); background: #f9fafb;"
                                            onclick="event.stopPropagation(); showParticulierInfoModalFromCard({{ $particulier->id }})">
                                            <i class="fas fa-info-circle text-muted"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <p class="text-muted text-xs mb-0">Aucun transporteur particulier agréé disponible pour le moment.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-3 text-xs px-4 py-2" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Details Particulier -->
    <div class="modal fade" id="particulierDetailsModal" tabindex="-1" aria-labelledby="particulierDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg overflow-hidden">
                <div class="modal-header border-0 bg-dark text-white p-4">
                    <h5 class="modal-title fw-bold" id="particulierDetailsModalLabel">
                        <i class="fas fa-user-circle text-orange me-2"></i> Informations du Transporteur Particulier
                    </h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background: #F8F9FA;">
                    <div class="row g-4">
                        <!-- Proprietor Column -->
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-white rounded-3 border border-gray-100 shadow-sm">
                                <div class="w-32 h-32 rounded-circle overflow-hidden mx-auto mb-3 border bg-gray-50 flex align-items-center justify-center" style="width: 120px; height: 120px; border-radius: 50%;">
                                    <img id="modalPartPhotoProprietaire" src="" alt="Photo" class="w-100 h-100 object-cover" style="border-radius: 50%;">
                                </div>
                                <h6 class="fw-bold mb-1 text-dark" id="modalPartName">Nom Chauffeur</h6>
                                <span class="badge bg-success-light text-success font-black text-xs uppercase px-2.5 py-1 rounded-3" style="font-size: 10px;">Agréé CAR225</span>
                            </div>
                        </div>
                        
                        <!-- Details Column -->
                        <div class="col-md-8">
                            <div class="p-4 bg-white rounded-3 border border-gray-100 shadow-sm h-100">
                                <h6 class="fw-bold border-bottom pb-2 text-dark font-black uppercase text-xs tracking-wider" style="font-size: 11px; margin-bottom: 12px;">
                                    <i class="fas fa-info-circle text-orange me-1"></i> Caractéristiques
                                </h6>
                                <div class="row g-3 text-sm mb-4">
                                    <div class="col-sm-6">
                                        <span class="text-muted block text-xs" style="font-size: 10px; display: block;">Immatriculation</span>
                                        <strong class="text-dark font-mono" id="modalPartImmatriculation" style="font-family: monospace;">--</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted block text-xs" style="font-size: 10px; display: block;">Nombre de places</span>
                                        <strong class="text-dark" id="modalPartPlaces">-- places</strong>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted block text-xs" style="font-size: 10px; display: block;">Mise en service</span>
                                        <strong class="text-dark" id="modalPartDateService">--</strong>
                                    </div>
                                </div>

                                <h6 class="fw-bold border-bottom pb-2 pt-3 text-dark font-black uppercase text-xs tracking-wider" style="font-size: 11px; margin-bottom: 12px;">
                                    <i class="fas fa-images text-orange me-1"></i> Photos du Véhicule
                                </h6>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <div class="border rounded overflow-hidden bg-gray-50" style="aspect-ratio: 4/3; height: 90px;">
                                            <img id="modalPartPhotoComplete" src="" alt="Complete" class="w-100 h-100 object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                        </div>
                                        <span class="text-center text-muted block mt-1" style="font-size: 9px; display: block;">Vue complète</span>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded overflow-hidden bg-gray-50" style="aspect-ratio: 4/3; height: 90px;">
                                            <img id="modalPartPhotoAvant" src="" alt="Avant" class="w-100 h-100 object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                        </div>
                                        <span class="text-center text-muted block mt-1" style="font-size: 9px; display: block;">Vue avant</span>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded overflow-hidden bg-gray-50" style="aspect-ratio: 4/3; height: 90px;">
                                            <img id="modalPartPhotoArriere" src="" alt="Arriere" class="w-100 h-100 object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                        </div>
                                        <span class="text-center text-muted block mt-1" style="font-size: 9px; display: block;">Vue arrière</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-white">
                    <button type="button" class="btn btn-secondary px-4 py-2 rounded-3 font-bold text-xs" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endsection
