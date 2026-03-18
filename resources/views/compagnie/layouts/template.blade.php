<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Espace Compagnie') — TransportCo</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vendors existants --}}
    <link rel="stylesheet" href="{{ asset('assetsPoster/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assetsPoster/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    {{-- Layout styles --}}
    <link rel="stylesheet" href="{{ asset('assetsPoster/assets/css/demo/style.css') }}">

    <link rel="shortcut icon" href="{{ asset('assetsPoster/assets/images/Car225_favicon.png') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @yield('styles')

    <style>
    /* ═══════════════════════════════════════════
       DESIGN SYSTEM — Variables & Reset
    ═══════════════════════════════════════════ */
    :root {
        --orange:        #F97316;
        --orange-dark:   #EA580C;
        --orange-light:  #FFF7ED;
        --orange-mid:    #FED7AA;
        --sidebar-w:     260px;
        --nav-h:         62px;
        --bg:            #F8F7F4;
        --surface:       #FFFFFF;
        --surface-2:     #F5F4F1;
        --border:        rgba(0,0,0,0.07);
        --border-strong: rgba(0,0,0,0.13);
        --text-1:        #1A1714;
        --text-2:        #6B6560;
        --text-3:        #A8A29E;
        --emerald:       #10B981;
        --blue:          #3B82F6;
        --red:           #EF4444;
        --amber:         #F59E0B;
        --radius:        14px;
        --radius-sm:     8px;
        --shadow-sm:     0 1px 3px rgba(0,0,0,0.06);
        --shadow-md:     0 4px 16px rgba(0,0,0,0.08);
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    html, body {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        background: var(--bg);
        color: var(--text-1);
        min-height: 100vh;
        font-size: 14px;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    /* ═══════════════════════════════════════════
       LAYOUT
    ═══════════════════════════════════════════ */
    .app-shell { display: flex; min-height: 100vh; }

    .app-content {
        margin-left: var(--sidebar-w);
        padding-top: var(--nav-h);
        min-height: 100vh;
        flex: 1;
        transition: margin-left 0.25s cubic-bezier(0.4,0,0.2,1);
    }

    /* ═══════════════════════════════════════════
       SIDEBAR
    ═══════════════════════════════════════════ */
    .sidebar {
        width: var(--sidebar-w);
        height: 100vh;
        position: fixed;
        top: 0; left: 0;
        background: var(--surface);
        border-right: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        z-index: 200;
        transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
        overflow: hidden;
    }

    /* Brand */
    .sidebar-brand { flex-shrink: 0; border-bottom: 1px solid var(--border); }
    .sidebar-brand-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0 18px;
        height: var(--nav-h);
        text-decoration: none;
        transition: background 0.15s;
    }
    .sidebar-brand-link:hover { background: var(--surface-2); }
    .sidebar-logo-img {
        width: 36px; height: 36px;
        border-radius: 9px;
        object-fit: cover;
        border: 1px solid var(--border-strong);
        background: white;
        flex-shrink: 0;
    }
    .sidebar-logo-fallback {
        width: 36px; height: 36px;
        border-radius: 9px;
        background: var(--orange);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 13px;
        flex-shrink: 0;
    }
    .sidebar-brand-text { overflow: hidden; }
    .sidebar-brand-name {
        display: block;
        font-weight: 800; font-size: 13px;
        color: var(--text-1);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        letter-spacing: -0.2px;
    }
    .sidebar-brand-role {
        display: block;
        font-size: 11px; font-weight: 500;
        color: var(--text-3);
    }

    /* Nav */
    .sidebar-nav {
        flex: 1;
        overflow-y: auto;
        padding: 10px 10px;
        scrollbar-width: thin;
        scrollbar-color: transparent transparent;
    }
    .sidebar-nav:hover { scrollbar-color: var(--border-strong) transparent; }
    .sidebar-nav::-webkit-scrollbar { width: 4px; }
    .sidebar-nav::-webkit-scrollbar-thumb { background: transparent; border-radius: 4px; }
    .sidebar-nav:hover::-webkit-scrollbar-thumb { background: var(--border-strong); }

    .nav-section-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.7px;
        text-transform: uppercase;
        color: var(--text-3);
        padding: 10px 8px 4px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 8px 10px;
        border-radius: var(--radius-sm);
        color: var(--text-2);
        font-size: 13px; font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        margin-bottom: 1px;
        user-select: none;
    }
    .nav-item:hover { background: var(--surface-2); color: var(--text-1); text-decoration: none; }
    .nav-item.active {
        background: var(--orange-light);
        color: var(--orange-dark);
    }
    .nav-item.active .nav-icon { color: var(--orange); }
    .nav-item.nav-item-danger { color: #B91C1C; }
    .nav-item.nav-item-danger .nav-icon { color: var(--red); }
    .nav-item.nav-item-danger:hover { background: #FEF2F2; }
    .nav-item.active-danger {
        background: #FEF2F2;
        color: #B91C1C;
    }
    .nav-item.nav-item-logout { color: var(--red); }
    .nav-item.nav-item-logout .nav-icon { color: var(--red); }
    .nav-item.nav-item-logout:hover { background: #FEF2F2; }

    .nav-icon {
        width: 17px;
        text-align: center;
        font-size: 12.5px;
        color: var(--text-3);
        flex-shrink: 0;
        transition: color 0.15s;
    }
    .nav-item:hover .nav-icon { color: var(--text-1); }
    .nav-item.active .nav-icon { color: var(--orange); }

    .nav-badge {
        margin-left: auto;
        background: var(--red);
        color: white;
        font-size: 10px; font-weight: 700;
        padding: 1px 6px;
        border-radius: 20px;
        min-width: 18px;
        text-align: center;
        flex-shrink: 0;
    }

    .nav-live-dot {
        width: 6px; height: 6px;
        background: var(--emerald);
        border-radius: 50%;
        margin-left: auto;
        flex-shrink: 0;
        animation: pulseGreen 2s ease-in-out infinite;
    }
    @keyframes pulseGreen {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:.5; transform:scale(.75); }
    }

    .nav-chevron {
        margin-left: auto;
        font-size: 10px;
        color: var(--text-3);
        transition: transform 0.22s ease;
        flex-shrink: 0;
    }
    .nav-has-sub.sub-open .nav-chevron { transform: rotate(90deg); }

    /* Submenu */
    .nav-sub-wrap {
        overflow: hidden;
        max-height: 0;
        transition: max-height 0.3s ease;
    }
    .nav-sub-wrap.open { max-height: 400px; }
    .nav-sub { padding: 2px 0 4px 28px; }
    .nav-sub-item {
        display: block;
        padding: 6px 10px;
        font-size: 12.5px; font-weight: 500;
        color: var(--text-2);
        text-decoration: none;
        border-radius: 6px;
        margin-bottom: 1px;
        position: relative;
        transition: background 0.12s, color 0.12s;
    }
    .nav-sub-item::before {
        content: '';
        position: absolute;
        left: -10px; top: 50%; transform: translateY(-50%);
        width: 3px; height: 3px;
        background: var(--text-3);
        border-radius: 50%;
        transition: background 0.15s;
    }
    .nav-sub-item:hover { background: var(--surface-2); color: var(--text-1); text-decoration: none; }
    .nav-sub-item:hover::before,
    .nav-sub-item.sub-active::before { background: var(--orange); }
    .nav-sub-item.sub-active { color: var(--orange-dark); font-weight: 600; }

    /* Footer */
    .sidebar-footer {
        flex-shrink: 0;
        padding: 8px 10px;
        border-top: 1px solid var(--border);
    }

    /* Backdrop */
    .sidebar-backdrop {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.35);
        z-index: 190;
        opacity: 0;
        transition: opacity 0.28s;
    }
    .sidebar-backdrop.open { opacity: 1; }

    /* ═══════════════════════════════════════════
       TOPBAR
    ═══════════════════════════════════════════ */
    .topbar {
        position: fixed;
        top: 0; left: var(--sidebar-w); right: 0;
        height: var(--nav-h);
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        padding: 0 24px;
        gap: 14px;
        z-index: 100;
        transition: left 0.25s cubic-bezier(0.4,0,0.2,1);
    }

    .topbar-burger {
        display: none;
        width: 34px; height: 34px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: transparent;
        align-items: center; justify-content: center;
        cursor: pointer;
        color: var(--text-2);
        font-size: 14px;
        flex-shrink: 0;
        transition: background 0.15s;
    }
    .topbar-burger:hover { background: var(--surface-2); }

    .topbar-title-wrap { flex: 1; min-width: 0; }
    .topbar-title {
        font-size: 15px; font-weight: 800;
        color: var(--text-1);
        letter-spacing: -0.2px;
        line-height: 1.2;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .topbar-breadcrumb {
        font-size: 11px; color: var(--text-3);
        font-weight: 500; display: block;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    .topbar-actions { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }

    .topbar-solde {
        display: flex; align-items: center; gap: 8px;
        background: var(--orange-light);
        border: 1px solid var(--orange-mid);
        border-radius: 10px;
        padding: 6px 12px;
        flex-shrink: 0;
    }
    .topbar-solde-icon { color: var(--orange); font-size: 13px; }
    .topbar-solde-label {
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
        color: var(--orange-dark); display: block; line-height: 1;
    }
    .topbar-solde-val {
        font-size: 14px; font-weight: 800;
        color: var(--orange-dark); display: block; line-height: 1.2;
    }
    .topbar-solde-unit { font-size: 11px; font-weight: 600; opacity: 0.7; }

    .topbar-icon-btn {
        width: 34px; height: 34px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: transparent;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; color: var(--text-2); font-size: 13px;
        text-decoration: none;
        position: relative;
        transition: background 0.15s, color 0.15s;
        flex-shrink: 0;
    }
    .topbar-icon-btn:hover { background: var(--surface-2); color: var(--text-1); }

    .topbar-notif-dot {
        position: absolute;
        top: 5px; right: 5px;
        width: 7px; height: 7px;
        background: var(--red);
        border-radius: 50%;
        border: 1.5px solid var(--surface);
    }

    /* Profile dropdown */
    .topbar-profile-wrap { position: relative; flex-shrink: 0; }
    .topbar-profile-btn {
        display: flex; align-items: center; gap: 7px;
        padding: 4px 10px 4px 4px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: transparent;
        cursor: pointer;
        transition: background 0.15s;
    }
    .topbar-profile-btn:hover { background: var(--surface-2); }
    .topbar-profile-img {
        width: 28px; height: 28px;
        border-radius: 50%; object-fit: cover;
        border: 1px solid var(--border-strong);
    }
    .topbar-profile-avatar {
        width: 28px; height: 28px;
        border-radius: 50%;
        background: var(--orange-light);
        border: 1px solid var(--orange-mid);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 11px;
        color: var(--orange-dark);
    }
    .topbar-profile-name { font-size: 12px; font-weight: 700; color: var(--text-1); }
    .topbar-profile-chevron { font-size: 9px; color: var(--text-3); }

    .topbar-dropdown {
        position: absolute;
        top: calc(100% + 8px); right: 0;
        background: var(--surface);
        border: 1px solid var(--border-strong);
        border-radius: var(--radius);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        min-width: 220px;
        padding: 6px 0;
        opacity: 0; visibility: hidden; transform: translateY(-6px);
        transition: opacity 0.18s, visibility 0.18s, transform 0.18s;
        z-index: 300;
    }
    .topbar-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0); }
    .topbar-dropdown-header { padding: 12px 16px 8px; }
    .topbar-dropdown-name { font-size: 13px; font-weight: 700; color: var(--text-1); }
    .topbar-dropdown-email { font-size: 11px; color: var(--text-3); margin-top: 2px; }
    .topbar-dropdown-divider { height: 1px; background: var(--border); margin: 4px 0; }
    .topbar-dropdown-item {
        display: flex; align-items: center; gap: 10px;
        padding: 9px 16px;
        font-size: 13px; font-weight: 600;
        color: var(--text-2);
        text-decoration: none;
        transition: background 0.12s, color 0.12s;
    }
    .topbar-dropdown-item:hover { background: var(--surface-2); color: var(--text-1); text-decoration: none; }
    .topbar-dropdown-item i { color: var(--text-3); font-size: 13px; width: 14px; text-align: center; }
    .topbar-dropdown-logout { color: var(--red) !important; }
    .topbar-dropdown-logout i { color: var(--red) !important; }
    .topbar-dropdown-logout:hover { background: #FEF2F2 !important; }

    /* ═══════════════════════════════════════════
       DASHBOARD PAGE
    ═══════════════════════════════════════════ */
    .dashboard-page {
        padding: 28px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Header */
    .dash-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        gap: 16px; flex-wrap: wrap;
        margin-bottom: 24px;
    }
    .dash-title { font-size: 26px; font-weight: 800; letter-spacing: -0.4px; color: var(--text-1); line-height: 1.2; }
    .dash-subtitle { font-size: 13px; color: var(--text-3); margin-top: 4px; font-weight: 500; }

    .dash-live-badge {
        display: flex; align-items: center; gap: 8px;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 12px; font-weight: 600;
        color: var(--text-2);
        flex-shrink: 0;
    }
    .dash-live-dot {
        width: 7px; height: 7px;
        background: var(--emerald);
        border-radius: 50%;
        animation: pulseGreen 2s ease-in-out infinite;
    }
    .dash-refresh-divider { width: 1px; height: 14px; background: var(--border-strong); }
    .dash-refresh-btn {
        background: transparent; border: none;
        color: var(--text-3); font-size: 12px;
        cursor: pointer; padding: 0;
        transition: color 0.15s;
    }
    .dash-refresh-btn:hover { color: var(--orange); }

    /* Metric Grid */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .metric-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        padding: 18px 18px 16px;
        transition: box-shadow 0.2s, transform 0.2s;
    }
    .metric-card:hover { box-shadow: var(--shadow-md); transform: translateY(-1px); }
    .metric-featured {
        background: linear-gradient(135deg, #F97316 0%, #EA580C 100%);
        border-color: transparent;
        box-shadow: 0 4px 20px rgba(249,115,22,0.25);
    }
    .metric-featured:hover { box-shadow: 0 8px 30px rgba(249,115,22,0.35); }

    .metric-top {
        display: flex; align-items: flex-start; justify-content: space-between;
        margin-bottom: 18px;
    }
    .metric-icon {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 15px;
    }
    .mi-green { background: #ECFDF5; color: #059669; }
    .mi-blue  { background: #EFF6FF; color: #2563EB; }
    .mi-rose  { background: #FFF1F2; color: #E11D48; }
    .mi-amber { background: #FFFBEB; color: #D97706; }
    .mi-white { background: rgba(255,255,255,0.2); color: white; }

    .metric-tag {
        font-size: 10px; font-weight: 700;
        letter-spacing: 0.4px; text-transform: uppercase;
        padding: 3px 7px; border-radius: 5px;
    }
    .mt-green { background: #ECFDF5; color: #065F46; }
    .mt-slate { background: #F1F5F9; color: #475569; }
    .mt-rose  { background: #FFF1F2; color: #9F1239; }
    .mt-amber { background: #FFFBEB; color: #92400E; }
    .mt-white { background: rgba(255,255,255,0.2); color: white; }

    .metric-label {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
        color: var(--text-3); margin-bottom: 5px;
    }
    .metric-featured .metric-label { color: rgba(255,255,255,0.7); }

    .metric-value {
        font-size: 24px; font-weight: 800;
        color: var(--text-1); letter-spacing: -0.5px; line-height: 1;
    }
    .metric-featured .metric-value { color: white; }
    .metric-unit { font-size: 12px; font-weight: 600; color: var(--text-3); margin-left: 2px; }
    .metric-featured .metric-unit { color: rgba(255,255,255,0.6); }

    /* Charts row */
    .dash-charts-row {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 16px;
        margin-bottom: 20px;
    }

    .dash-card {
        background: var(--surface);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
    }
    .dash-card-column { display: flex; flex-direction: column; }

    .dash-card-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid var(--border);
        flex-shrink: 0;
    }
    .dash-card-head-left { display: flex; align-items: center; gap: 10px; }
    .dash-card-icon {
        width: 30px; height: 30px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        font-size: 12px;
    }
    .dci-green { background: #ECFDF5; color: #059669; }
    .dci-blue  { background: #EFF6FF; color: #2563EB; }
    .dci-red   { background: #FFF1F2; color: #E11D48; }

    .dash-card-title {
        font-size: 12px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.3px;
        color: var(--text-1);
    }
    .dash-card-tag {
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.4px;
        color: var(--text-3);
        background: var(--surface-2);
        padding: 3px 8px; border-radius: 5px;
    }
    .dash-card-action {
        font-size: 11px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.4px;
        color: var(--orange); text-decoration: none;
        padding: 4px 9px; border-radius: 6px;
        transition: background 0.15s;
    }
    .dash-card-action:hover { background: var(--orange-light); text-decoration: none; }

    .dash-chart-body { padding: 18px; height: 280px; position: relative; }

    /* Signalements */
    .sig-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
        scrollbar-width: thin;
        scrollbar-color: var(--border) transparent;
    }
    .sig-item {
        display: block;
        padding: 11px 11px;
        border-radius: 9px;
        margin-bottom: 2px;
        text-decoration: none;
        transition: background 0.12s;
        border: 1px solid transparent;
    }
    .sig-item:hover { background: var(--surface-2); text-decoration: none; }
    .sig-unread {
        background: #FFF5F5;
        border-color: #FEE2E2;
    }
    .sig-unread:hover { background: #FEE2E2; }

    .sig-item-top {
        display: flex; align-items: center; gap: 6px;
        margin-bottom: 6px;
    }
    .sig-type-badge {
        font-size: 9px; font-weight: 800;
        text-transform: uppercase; letter-spacing: 0.4px;
        padding: 2px 6px;
        background: #FFF1F2; color: #9F1239;
        border-radius: 4px; border: 1px solid #FEE2E2;
        flex-shrink: 0;
    }
    .sig-dot {
        width: 6px; height: 6px;
        background: var(--red); border-radius: 50%;
        flex-shrink: 0;
        animation: pulseGreen 1.8s ease-in-out infinite;
    }
    .sig-time { font-size: 11px; color: var(--text-3); font-weight: 500; margin-left: auto; }
    .sig-desc {
        font-size: 12px; color: var(--text-2); line-height: 1.5;
        margin-bottom: 7px;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .sig-author { display: flex; align-items: center; gap: 6px; }
    .sig-avatar {
        width: 18px; height: 18px; border-radius: 50%;
        background: var(--surface-2);
        display: flex; align-items: center; justify-content: center;
        font-size: 9px; font-weight: 700; color: var(--text-2);
    }
    .sig-author-name { font-size: 11px; font-weight: 600; color: var(--text-2); }

    .sig-empty {
        display: flex; flex-direction: column; align-items: center;
        padding: 30px 16px; text-align: center;
    }
    .sig-empty-icon {
        width: 44px; height: 44px; border-radius: 50%;
        background: #ECFDF5; color: var(--emerald);
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; margin-bottom: 10px;
    }
    .sig-empty-title { font-size: 13px; font-weight: 700; color: var(--text-1); margin-bottom: 4px; }
    .sig-empty-sub { font-size: 12px; color: var(--text-3); }

    /* Table */
    .dash-table-wrap { overflow-x: auto; }
    .dash-table { width: 100%; border-collapse: collapse; }
    .dash-table thead tr { background: var(--surface-2); }
    .dash-table th {
        padding: 11px 18px;
        font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: 0.5px;
        color: var(--text-3);
        white-space: nowrap;
        text-align: left;
    }
    .dash-table tbody tr { border-bottom: 1px solid var(--border); transition: background 0.1s; }
    .dash-table tbody tr:last-child { border-bottom: none; }
    .dash-table tbody tr:hover { background: #FAFAF9; }
    .dash-table td { padding: 13px 18px; vertical-align: middle; }
    .text-right { text-align: right !important; }
    .text-center { text-align: center !important; }

    .td-user { display: flex; align-items: center; gap: 10px; }
    .td-avatar {
        width: 32px; height: 32px; border-radius: 50%;
        background: var(--surface-2);
        border: 1px solid var(--border-strong);
        display: flex; align-items: center; justify-content: center;
        font-size: 12px; font-weight: 700; color: var(--text-2);
        flex-shrink: 0;
    }
    .td-name { font-size: 13px; font-weight: 700; color: var(--text-1); }
    .td-phone { font-size: 11px; color: var(--text-3); }

    .route-pill {
        display: inline-flex; align-items: center; gap: 5px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 7px;
        padding: 5px 9px;
        font-size: 12px; font-weight: 600; color: var(--text-2);
    }
    .route-arrow { color: var(--text-3); font-size: 9px; }
    .route-time-badge {
        background: #EFF6FF; color: #1D4ED8;
        font-size: 10px; font-weight: 700;
        padding: 2px 5px; border-radius: 4px;
        margin-left: 3px;
    }
    .td-amount { font-size: 13px; font-weight: 800; color: var(--text-1); white-space: nowrap; }
    .td-unit { font-size: 11px; font-weight: 500; color: var(--text-3); }
    .td-date { font-size: 12px; font-weight: 600; color: var(--text-2); }

    .table-empty {
        display: flex; flex-direction: column; align-items: center;
        padding: 32px; color: var(--text-3); gap: 8px;
    }
    .table-empty-icon { font-size: 22px; }
    .table-empty p { font-size: 13px; font-weight: 600; }

    /* ═══════════════════════════════════════════
       RESPONSIVE
    ═══════════════════════════════════════════ */
    @media (max-width: 1280px) {
        .metric-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 1024px) {
        .dash-charts-row { grid-template-columns: 1fr; }
        .dashboard-page { padding: 20px; }
    }
    @media (max-width: 992px) {
        .sidebar {
            transform: translateX(-100%);
            box-shadow: none;
        }
        .sidebar.open {
            transform: translateX(0);
            box-shadow: 4px 0 30px rgba(0,0,0,0.12);
        }
        .sidebar-backdrop { display: block; pointer-events: none; }
        .sidebar-backdrop.open { pointer-events: all; }
        .topbar { left: 0 !important; }
        .app-content { margin-left: 0; }
        .topbar-burger { display: flex; }
    }
    @media (max-width: 640px) {
        .metric-grid { grid-template-columns: repeat(2, 1fr); }
        .dashboard-page { padding: 16px; }
        .topbar { padding: 0 16px; gap: 10px; }
        .topbar-breadcrumb { display: none; }
        .topbar-solde-label { display: none; }
        .dash-title { font-size: 20px; }
    }
    @media (max-width: 400px) {
        .metric-grid { grid-template-columns: 1fr 1fr; }
    }
    </style>
</head>
<body>
<script src="{{ asset('assetsPoster/assets/js/preloader.js') }}"></script>

<div class="app-shell">

    {{-- SIDEBAR --}}
    @include('compagnie.layouts.sidebar')

    {{-- MAIN WRAPPER --}}
    <div class="app-content">

        {{-- NAVBAR --}}
        @include('compagnie.layouts.navbar')

        {{-- PAGE CONTENT --}}
        <div>
            @yield('content')
        </div>

    </div>
</div>

{{-- Vendors JS --}}
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="{{ asset('assetsPoster/assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('assetsPoster/assets/js/material.js') }}"></script>
<script src="{{ asset('assetsPoster/assets/js/misc.js') }}"></script>
<script src="{{ asset('assetsPoster/assets/js/dashboard.js') }}"></script>

<script>
/* ── Sidebar mobile ── */
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    const bd = document.getElementById('sidebarBackdrop');
    if (bd) { bd.classList.add('open'); }
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    const bd = document.getElementById('sidebarBackdrop');
    if (bd) { bd.classList.remove('open'); }
}

/* ── Submenu toggle ── */
function toggleNavSub(item) {
    const wrap = item.nextElementSibling;
    if (!wrap || !wrap.classList.contains('nav-sub-wrap')) return;
    const isOpen = wrap.classList.contains('open');

    // Fermer tous
    document.querySelectorAll('.nav-sub-wrap.open').forEach(w => w.classList.remove('open'));
    document.querySelectorAll('.nav-has-sub.sub-open').forEach(i => i.classList.remove('sub-open'));

    if (!isOpen) {
        wrap.classList.add('open');
        item.classList.add('sub-open');
    }
}

/* ── Profile dropdown ── */
function toggleProfileMenu() {
    document.getElementById('topbarDropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('topbarProfileWrap');
    if (wrap && !wrap.contains(e.target)) {
        const dd = document.getElementById('topbarDropdown');
        if (dd) dd.classList.remove('open');
    }
});

/* ── Ouvrir le sous-menu actif au chargement ── */
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.nav-has-sub.sub-open').forEach(function(item) {
        const wrap = item.nextElementSibling;
        if (wrap && wrap.classList.contains('nav-sub-wrap')) {
            wrap.classList.add('open');
        }
    });
});
</script>

@yield('scripts')
</body>
</html>