<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: #ffeaca">
    <div class="mdc-drawer__header">
        <a href="{{ route('user.dashboard') }}" class="brand-logo">
            @auth
                @if (Auth::user()->photo_profile_path)
                    <img src="{{ asset('storage/' . Auth::user()->photo_profile_path) }}"
                        style="width: 50%; margin-left: 50px; border-radius: 50%; object-fit: cover;" alt="Photo de profil">
                @else
                    <div style="width: 50%; margin-left: 50px; display: flex; align-items: center; justify-content: center;">
                        <div
                            style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(to right, #e94f1b, #d94818); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; font-weight: bold;">
                            {{ strtoupper(substr(Auth::user()->prenom, 0, 1)) }}{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </div>
                @endif
            @else
                <img src="{{ asset('assetsPoster/assets/images/logo_car225.png') }}" style="width: 50%; margin-left: 50px" alt="logo">
            @endauth
        </a>
    </div>
    <div class="mdc-drawer__content">
        <div class="user-info">
            <p class="name text-center text-black"> {{ Auth::guard('web')->user()->name }}
                {{ Auth::guard('web')->user()->prenom }}</p>
            <p class="email text-center text-black">{{ Auth::guard('web')->user()->email }}</p>
        </div>
        <div class="mdc-list-group">
            <nav class="mdc-list mdc-drawer-menu">
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('user.dashboard') }}">
                        <i class="fas fa-home mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Tableau de bord
                    </a>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('user.wallet.index') }}">
                        <i class="fas fa-wallet mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                        Compte
                    </a>
                </div>
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="#">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">save</i>
                Demandes 
              </a>
            </div> --}}
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('user.permanent-personnel.create')}}">
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
                        <i class="fas fa-file-signature mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true"></i>
                        Réservation
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-perso">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('reservation.create')}}">
                                    Réserver
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="{{route('reservation.index')}}">
                                    Liste
                                </a>
                            </div>
                        </nav>
                    </div>
                </div>
                <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="{{ route('signalement.create') }}">
                        <i class="fas fa-exclamation-triangle mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                       Signaler un problème
                    </a>
                </div>
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="{{route('demande-recuperation.create')}}">
                <i class="fas fa-truck-pickup mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
                Recupération 
              </a>
            </div> --}}
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-drawer-link" href="#">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">home</i>
                Mes factures
              </a>
            </div> --}}
                <!-- <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel"
                        data-target="ui-sub-agent">
                        <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon"
                            aria-hidden="true">home</i>
                        Depot/Recupération
                        <i class="mdc-drawer-arrow material-icons">chevron_right</i>
                    </a>
                    <div class="mdc-expansion-panel" id="ui-sub-agent">
                        <nav class="mdc-list mdc-drawer-submenu">
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Demande
                                </a>
                            </div>
                            <div class="mdc-list-item mdc-drawer-item">
                                <a class="mdc-drawer-link" href="#">
                                    Liste
                                </a>
                            </div>
                            {{-- <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Présences
                    </a>
                  </div> --}}
                        </nav>
                    </div>
                </div> -->
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-menu">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">home</i>
                Structure
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-menu">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Ajout Structure
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Liste Structure
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Structure desactivées
                    </a>
                  </div>
                </nav>
              </div>
            </div> --}}
                {{-- <div class="mdc-list-item mdc-drawer-item">
              <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-code">
                <i class="material-icons mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true">home</i>
                Code QR Accès
                <i class="mdc-drawer-arrow material-icons">chevron_right</i>
              </a>
              <div class="mdc-expansion-panel" id="ui-sub-code">
                <nav class="mdc-list mdc-drawer-submenu">
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Ajouter
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Code QR accès
                    </a>
                  </div>
                  <div class="mdc-list-item mdc-drawer-item">
                    <a class="mdc-drawer-link" href="#">
                      Structure desactivées
                    </a>
                  </div>
                </nav>
              </div> --}}
        </div>
    </div>
</aside>
