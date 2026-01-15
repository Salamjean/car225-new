<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: red">
    <div class="mdc-drawer__header" style="padding: 20px 0;">
        <a href="{{ route('compagnie.dashboard') }}" class="brand-logo d-flex justify-content-center align-items-center">
            @if (Auth::guard('compagnie')->user()->path_logo)
                <img src="{{ asset('storage/' . Auth::guard('compagnie')->user()->path_logo) }}"
                    style="width: 120px; height: 120px; object-fit: contain; border-radius: 10px; border: 3px solid white; background: white;"
                    alt="{{ Auth::guard('compagnie')->user()->name }}">
            @else
                <div class="default-company-logo d-flex align-items-center justify-content-center text-white font-weight-bold"
                    style="width: 120px; height: 120px; background: #fea219; border-radius: 10px; font-size: 2rem; border: 3px solid white;">
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
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="#">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">save</i>
                Demandes 
              </a>
            </div> --}}
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('compagnie.permanent-personnel.create')}}">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">dashboard</i>
                Personnel permanent
              </a>
            </div> --}}
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="#">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">dashboard</i>
                Historiques des visites
              </a>
            </div> --}}
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
                            {{-- <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Structure desactivées
                    </a>
                  </div> --}}
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-agent">
                        <i class="fas fa-car mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
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
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-colis">
                        <i class="fas fa-user-tie mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
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
                        data-target="ui-sub-menu">
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
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-code">
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
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                        <i class="fas fa-user-tie mdc-list-item__start-detail mdc-drawer-item-icon"></i>
                        Chauffeur
                    </a>
                </div>

                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-pro">
                        <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Programme
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-pro">
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
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-client">
                        <i class="fas fa-calendar-alt mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Type Client
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-client">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Client
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Prospect
                                </a>
                            </div>
                            {{-- <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Structure desactivées
                    </a>
                  </div> --}}
                        </nav>
                    </div>
                </div>

                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                        <i class="fas fa-box mdc-list-item__start-detail mdc-drawer-item-icon"></i>
                        Récuperation demandé
                    </a>
                </div>
        </div>
</aside>
