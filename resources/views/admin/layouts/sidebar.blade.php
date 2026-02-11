<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open" style="background-color: red">
  <div class="mdc-drawer__header">
    <a href="{{route('admin.dashboard')}}" class="brand-logo">
      <img src="{{asset('assetsPoster/assets/images/logo_car225.png')}}" style="width: 50%; margin-left: 50px"
        alt="logo">
    </a>
  </div>
  <div class="mdc-drawer__content">
    <div class="user-info">
      <p class="name text-center text-black"> {{Auth::guard('admin')->user()->name}} </p>
      <p class="email text-center text-black">{{Auth::guard('admin')->user()->email}}</p>
    </div>
    <div class="mdc-list-group">
      <nav class="mdc-list mdc-drawer-menu">
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.dashboard')}}">
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
          <a class="mdc-drawer-link" href="{{route('admin.permanent-personnel.create')}}">
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
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-perso">
            <i class="fas fa-building mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Compagnie
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-perso">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('compagnie.create')}}">
                  Ajouter
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('compagnie.index')}}">
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
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-pompier">
            <i class="fas fa-fire-extinguisher mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Sapeurs Pompiers
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-pompier">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('sapeur-pompier.create')}}">
                  Ajouter
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('sapeur-pompier.index')}}">
                  Liste
                </a>
              </div>
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-hotesse">
            <i class="fas fa-user-tie mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            Gestion Hôtesses
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-hotesse">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.hotesse.create')}}">
                  Ajouter
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.hotesse.index')}}">
                  Liste
                </a>
              </div>
            </nav>
          </div>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.itineraire.index')}}">
            <i class="fas fa-route mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Itinéraire
          </a>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.notifications.index')}}">
            <i class="fas fa-bell mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Notifications
          </a>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.support.index')}}">
            <i class="fas fa-headset mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Support Client
          </a>
        </div>
    </div>
</aside>