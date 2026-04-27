<aside class="mdc-drawer mdc-drawer--dismissible mdc-drawer--open">
  <div class="mdc-drawer__header">
    <a href="{{route('admin.dashboard')}}" class="brand-logo">
      <img src="{{asset('assetsPoster/assets/images/logo_car225.png')}}" alt="logo">
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
        <div class="mdc-list-item mdc-drawer-item" id="historique-menu-item">
          <a class="mdc-drawer-link" href="{{route('admin.revenus.tickets')}}">
            <i class="fas fa-history mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Historique Global
          </a>
        </div>
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
              @if(\App\Models\Setting::isTicketSystemEnabled())
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('compagnie.recharge.index')}}">
                   Rechargeur
                </a>
              </div>
              @endif
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('compagnie.index')}}">
                  Liste
                </a>
              </div>
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
          <a class="mdc-expansion-panel-link" href="#" data-toggle="expansionPanel" data-target="ui-sub-onpc">
            <i class="fas fa-shield-alt mdc-list-item__start-detail mdc-drawer-item-icon" aria-hidden="true"></i>
            ONPC
            <i class="mdc-drawer-arrow material-icons">chevron_right</i>
          </a>
          <div class="mdc-expansion-panel" id="ui-sub-onpc">
            <nav class="mdc-list mdc-drawer-submenu">
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.onpc.create')}}">
                  Ajouter
                </a>
              </div>
              <div class="mdc-list-item mdc-drawer-item">
                <a class="mdc-drawer-link" href="{{route('admin.onpc.index')}}">
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
          <a class="mdc-drawer-link" href="{{route('admin.voyages.en-cours')}}" style="display: flex; align-items: center; gap: 4px;">
            <i class="fas fa-satellite-dish mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Voyages en cours
            <span style="display:inline-flex;align-items:center;justify-content:center;width:8px;height:8px;border-radius:50%;background:#22c55e;margin-left:4px;animation:pulse-dot 2s infinite;"></span>
          </a>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.notifications.index')}}">
            <i class="fas fa-bell mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Notifications
          </a>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.support.index')}}" style="display: flex; align-items: center; gap: 4px;">
            <i class="fas fa-headset mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Support Client
            @php
              $supportCount = \App\Models\SupportRequest::where('statut', 'ouvert')->count();
            @endphp
            @if($supportCount > 0)
              <span class="badge badge-danger" style="margin-left: auto; border-radius: 12px; font-size: 10px; padding: 2px 6px; background-color: #dc3545; color: white;">{{ $supportCount }}</span>
            @endif
          </a>
        </div>
        <div class="mdc-list-item mdc-drawer-item">
          <a class="mdc-drawer-link" href="{{route('admin.settings.index')}}">
            <i class="fas fa-cog mdc-list-item__start-detail mdc-drawer-item-icon"></i>
            Paramètres
          </a>
        </div>
      </nav>
    </div>
  </div>
</aside>
<!-- Sidebar backdrop for mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>