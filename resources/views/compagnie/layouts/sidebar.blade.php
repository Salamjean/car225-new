<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: red">
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
                        data-target="ui-sub-agent">
                        <i class="fas fa-car mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Véhicule
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-agent">
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
                
               
                
                <!-- Onglet Signalements -->
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('compagnie.signalements.index') }}">
                        <i class="fas fa-exclamation-triangle mdc-list-item__start-detail mdc-drawer-item-icon text-danger"
                            aria-hidden="true" style="color:red"></i>
                        Signalements
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
</aside>