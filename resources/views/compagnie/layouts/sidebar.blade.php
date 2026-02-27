<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open">
    <div class="mdc-drawer__header" style="padding: 20px 0;">
        <a href="{{ route('compagnie.dashboard') }}"
            class="brand-logo d-flex justify-content-center align-items-center">
            @if (Auth::guard('compagnie')->user()->path_logo)
                <img src="{{ asset('storage/' . Auth::guard('compagnie')->user()->path_logo) }}"
                    style="width: 120px; height: 120px; object-fit: contain; border-radius: 10px; border: 3px solid white; background: white;"
                    alt="{{ Auth::guard('compagnie')->user()->name }}">
            @else
                <div class="default-company-logo d-flex align-items-center justify-content-center text-white font-weight-bold"
                    style="width: 120px; height: 120px; background: #e94f1b; border-radius: 10px; font-size: 2rem; border: 3px solid white;">
                    {{ substr(Auth::guard('compagnie')->user()->name, 0, 2) }}
                </div>
            @endif
        </a>
    </div>
    <div class="mdc-drawer__content">
        <div class="user-info">
            <p class="name text-center text-black"> {{ Auth::guard('compagnie')->user()->name }} </p>
            <p class="email text-center text-black">{{ Auth::guard('compagnie')->user()->email }}</p>
        </div>
        <div class="mdc-list-group">
            <nav class="mdc-list mdc-drawer-menu">
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('compagnie.dashboard') }}">
                        <i class="fas fa-home mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Tableau de bord
                    </a>
                </div>
                      
                <!-- Suivi GPS en temps réel -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link d-flex align-items-center" href="{{ route('compagnie.tracking.index') }}">
                        <i class="fas fa-map-marked-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true" style="color: #10b981;"></i>
                        Voyages en cours
                        <span class="ml-auto" style="width: 8px; height: 8px; background: #10b981; border-radius: 50; display: inline-block; animation: pulse 2s infinite;"></span>
                    </a>
                </div>
               
                  <!-- Onglet Messages -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link d-flex align-items-center justify-content-between w-100" href="{{ route('compagnie.messages.index') }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-envelope mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                            Messages
                        </div>
                        @php
                            $unreadGareMessages = Auth::guard('compagnie')->user()->receivedGareMessages()->where('is_read', false)->count();
                        @endphp
                        @if($unreadGareMessages > 0)
                            <span class="badge badge-pill badge-danger" style="background: #e94f1b; font-weight: bold; padding: 4px 8px;">
                                {{ $unreadGareMessages }}
                            </span>
                        @endif
                    </a>
                </div>

                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-reservations">
                        <i class="fas fa-ticket-alt mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Réservations
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-reservations">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link"
                                    href="{{ route('company.reservation.index', ['tab' => 'en-cours']) }}">
                                    En cours
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link"
                                    href="{{ route('company.reservation.index', ['tab' => 'terminees']) }}">
                                    Terminées
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link"
                                    href="{{ route('company.reservation.details') }}">
                                    Détails & Stats
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-conteneur">
                        <i class="fas fa-boxes mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Programmation
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-conteneur">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('programme.create')}}">
                                    Ajouter
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('programme.index')}}">
                                    Listes
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('programme.history')}}">
                                    Historiques
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-agentss">
                        <i class="fas fa-users mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Agent
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-agentss">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link"
                                    href="{{ route('compagnie.agents.create') }}">
                                    Ajouter
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link"
                                    href="{{ route('compagnie.agents.index') }}">
                                    Liste
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-colis">
                        <i class="fas fa-user-tie mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Personnel
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-colis">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('personnel.create')}}">
                                    Ajouter
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('personnel.index')}}">
                                    Liste
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                 <!-- Gestion Caissières -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-caisses">
                        <i class="fas fa-cash-register mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Gestion Caissières
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-caisses">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{ route('compagnie.caisse.create') }}">
                                    Ajouter
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{ route('compagnie.caisse.index') }}">
                                    Liste
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-vehicule">
                        <i class="fas fa-bus mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Véhicules
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-vehicule">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('vehicule.create')}}">
                                    Ajouter
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('vehicule.index')}}">
                                    Liste
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-perso">
                        <i class="fas fa-route mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Itinéraire
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-perso">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('itineraire.create')}}">
                                    Ajouter
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('itineraire.index')}}">
                                    Liste
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-gare">
                        <i class="fas fa-building mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Gares
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-gare">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('gare.create')}}">
                                    Ajouter
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('gare.index')}}">
                                    Liste
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                
                <!-- Onglet Profil -->
                <!-- <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('compagnie.profile') }}">
                        <i class="fas fa-id-card mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Mon Profil
                    </a>
                </div> -->

              
                <!-- Onglet Signalements -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link d-flex align-items-center justify-content-between w-100" href="{{ route('compagnie.signalements.index') }}">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle mdc-list-item__start-detail mdc-drawer-item-icon text-danger"
                                aria-hidden="true" style="color:red"></i>
                            Signalements
                        </div>
                        @php
                            $newSignalements = \App\Models\Signalement::whereHas('programme', function ($q) {
                                $q->where('compagnie_id', Auth::guard('compagnie')->id());
                            })
                            ->where('is_read_by_company', false)
                            ->count();
                        @endphp
                        @if($newSignalements > 0)
                            <span class="badge badge-pill badge-danger sidebar-signalement-badge" style="background: #ef4444; font-weight: bold; padding: 4px 8px;">
                                {{ $newSignalements }}
                            </span>
                        @endif
                    </a>
                </div>
                <!-- <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-menu">
                        <i class="fas fa-qrcode mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Scanner
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-menu">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Mise en entrépot
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Chargement
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Déchargment
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-code">
                        <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Planning voyage
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-code">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Planifier
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Liste
                                </a>
                            </div>
                            {{-- <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Structure desactivées
                                </a>
                            </div> --}}
                        </nav>
                    </div>
                </div> -->
        </div>

        <style>
            @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
            
            /* Styles premium pour la Sidebar */
            .mdc-drawer {
                background-color: #ffffff !important;
                border-right: 1px solid #f1f5f9 !important;
                box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02) !important;
                font-family: 'Inter', sans-serif;
            }
            .user-info {
                background: linear-gradient(to bottom, #f8fafc, #ffffff);
                padding: 24px 0 16px 0 !important;
                border-bottom: 1px solid #f1f5f9;
                margin-bottom: 16px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.01);
            }
            .user-info .name {
                font-weight: 800 !important;
                color: #0f172a !important;
                font-size: 0.95rem !important;
                letter-spacing: -0.2px;
                margin-bottom: 4px;
            }
            .user-info .email {
                color: #64748b !important;
                font-size: 0.70rem !important;
                font-weight: 600 !important;
                letter-spacing: 0.5px;
            }
            .brand-logo img, .default-company-logo {
                box-shadow: 0 4px 14px rgba(234, 88, 12, 0.15) !important;
                border: 2px solid #ffffff !important;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .brand-logo:hover img, .brand-logo:hover .default-company-logo {
                transform: scale(1.05) translateY(-2px);
            }
            
            /* Elements de la liste */
            .mdc-drawer .mdc-list-item {
                margin: 4px 16px !important;
                border-radius: 12px !important;
                height: auto !important; /* Autoriser l'extension pour les sous-menus */
                min-height: 46px !important;
                display: block !important; /* Pour que le contenu s'empile verticalement */
                padding: 0 !important;
                transition: all 0.2s ease;
                overflow: visible !important;
            }
            .mdc-drawer .mdc-list-item:hover {
                background-color: #fff7ed !important;
            }
            .mdc-drawer-link, .mdc-expansion-panel-link {
                height: 46px !important;
                display: flex !important;
                align-items: center;
                padding: 0 16px !important;
                text-decoration: none !important;
            }
            .mdc-drawer .mdc-list-item:hover > .mdc-drawer-link,
            .mdc-drawer .mdc-list-item:hover > .mdc-expansion-panel-link {
                color: #ea580c !important;
            }
            .mdc-drawer .mdc-list-item:hover .mdc-drawer-item-icon {
                color: #ea580c !important;
                transform: scale(1.1);
            }
            
            /* Liens et Texte */
            .mdc-drawer-link, .mdc-expansion-panel-link {
                color: #475569 !important; /* Slate 600 */
                font-weight: 600 !important;
                font-size: 0.825rem !important;
                letter-spacing: 0.3px;
                width: 100%;
                transition: color 0.2s ease;
            }
            .mdc-drawer-item-icon {
                color: #94a3b8 !important; /* Slate 400 */
                font-size: 1.15rem !important;
                width: 24px;
                text-align: center;
                margin-right: 14px !important;
                transition: all 0.2s ease;
            }

            /* Badges Notifications */
            .badge-danger {
                background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
                box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25) !important;
                border: none !important;
                padding: 4px 8px !important;
                border-radius: 20px !important;
                font-size: 0.65rem !important;
                font-weight: 800 !important;
            }

            /* Flèche des sous-menus */
            .mdc-drawer-arrow {
                color: #cbd5e1 !important;
                transition: transform 0.3s ease, color 0.2s ease;
                margin-left: auto !important;
            }
            .mdc-list-item:hover .mdc-drawer-arrow {
                color: #ea580c !important;
            }

            /* Sous-menus */
            .mdc-expansion-panel {
                background-color: transparent !important;
                padding-top: 0px;
                padding-bottom: 8px;
                display: none; /* Par défaut via le template JS, mais on s'assure de la cohérence */
            }
            /* Lorsque le panel est ouvert (géré par misc.js via la classe 'expanded' ou autre, 
               mais le template utilise souvent le style direct height. 
               On s'assure juste que si display est block, il s'affiche bien) */
            
            .mdc-drawer-submenu .mdc-list-item {
                margin: 2px 12px 2px 32px !important;
                height: 38px !important;
                min-height: 38px !important;
                border-radius: 8px !important;
                display: flex !important; /* Les sous-items restent en flex */
                align-items: center !important;
            }
            .mdc-drawer-submenu .mdc-drawer-link {
                font-weight: 500 !important;
                color: #64748b !important;
                font-size: 0.8rem !important;
                position: relative;
                transition: all 0.2s ease;
            }
            .mdc-drawer-submenu .mdc-drawer-link::before {
                content: '';
                position: absolute;
                left: -16px;
                top: 50%;
                transform: translateY(-50%);
                width: 4px;
                height: 4px;
                background-color: #cbd5e1;
                border-radius: 50%;
                transition: all 0.2s ease;
            }
            .mdc-drawer-submenu .mdc-list-item:hover {
                background-color: #f8fafc !important;
            }
            .mdc-drawer-submenu .mdc-list-item:hover .mdc-drawer-link {
                color: #ea580c !important;
                padding-left: 4px;
            }
            .mdc-drawer-submenu .mdc-list-item:hover .mdc-drawer-link::before {
                background-color: #ea580c;
                transform: translateY(-50%) scale(1.5);
            }
        </style>
</aside>