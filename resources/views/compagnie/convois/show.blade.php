@extends('compagnie.layouts.template')

@section('page-title', 'Détail Convoi')
@section('page-subtitle', 'Gestion de la demande de convoi')

@section('styles')
<style>
/* ──────────────────────────────────────────────
   SHOW CONVOI — Design Premium (Bootstrap-safe)
   ────────────────────────────────────────────── */
.sc-page { padding: 28px 32px 60px; max-width: 1120px; }

/* ── Back link ── */
.sc-back {
    display: inline-flex; align-items: center; gap: 8px;
    color: #6b7280; font-size: 12px; font-weight: 700;
    text-decoration: none; margin-bottom: 16px;
    transition: color .2s;
}
.sc-back:hover { color: #e94f1b; text-decoration: none; }
.sc-back-icon {
    width: 28px; height: 28px; border-radius: 50%;
    background: #fff; border: 1px solid #e5e7eb;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; color: #6b7280;
    transition: background .2s, border-color .2s, color .2s;
}
.sc-back:hover .sc-back-icon { background: #FFF7F5; border-color: #e94f1b; color: #e94f1b; }

/* ── Page Header ── */
.sc-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 28px; flex-wrap: wrap; }
.sc-title { font-size: 30px; font-weight: 900; color: #111827; margin: 0; letter-spacing: -0.5px; }
.sc-title span { color: #e94f1b; }
.sc-ref { display: inline-block; margin-top: 6px; background: #f3f4f6; color: #4b5563; font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; letter-spacing: .3px; }

/* ── Status Badge ── */
.sc-status {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 20px; border-radius: 50px;
    font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
}
.sc-status-dot {
    width: 8px; height: 8px; border-radius: 50%;
    animation: pulse-sm 2s infinite;
}
@keyframes pulse-sm { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.7)} }
.status-en_attente { background: #fef3c7; color: #92400e; }
.status-en_attente .sc-status-dot { background: #f59e0b; }
.status-valide { background: #dbeafe; color: #1e3a8a; }
.status-valide .sc-status-dot { background: #3b82f6; }
.status-paye { background: #d1fae5; color: #065f46; }
.status-paye .sc-status-dot { background: #10b981; }
.status-en_cours { background: #e0e7ff; color: #3730a3; }
.status-en_cours .sc-status-dot { background: #6366f1; }
.status-termine { background: #f3f4f6; color: #374151; }
.status-termine .sc-status-dot { background: #9ca3af; }
.status-refuse, .status-annule { background: #fee2e2; color: #7f1d1d; }
.status-refuse .sc-status-dot, .status-annule .sc-status-dot { background: #ef4444; }

/* ── Alerts ── */
.sc-alert { display: flex; align-items: flex-start; gap: 14px; padding: 16px 20px; border-radius: 14px; margin-bottom: 20px; }
.sc-alert-icon { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
.sc-alert-title { font-size: 13px; font-weight: 800; margin-bottom: 2px; }
.sc-alert-body  { font-size: 13px; font-weight: 500; }
.sc-alert-success { background: #ecfdf5; border: 1px solid #bbf7d0; color: #065f46; }
.sc-alert-error   { background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d; }

/* ── Hero Banner (Itinéraire) ── */
.sc-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #1a1030 100%);
    border-radius: 24px; padding: 36px 40px; margin-bottom: 24px;
    position: relative; overflow: hidden; color: #fff;
}
.sc-hero::before {
    content: ''; position: absolute; top: -80px; right: -80px;
    width: 320px; height: 320px; border-radius: 50%;
    background: radial-gradient(circle, rgba(233,79,27,.35) 0%, transparent 70%);
    pointer-events: none;
}
.sc-hero::after {
    content: ''; position: absolute; bottom: -60px; left: -60px;
    width: 240px; height: 240px; border-radius: 50%;
    background: radial-gradient(circle, rgba(99,102,241,.25) 0%, transparent 70%);
    pointer-events: none;
}
.sc-hero-inner { position: relative; z-index: 1; }
.sc-hero-label {
    font-size: 10px; font-weight: 800; letter-spacing: 2px;
    text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.sc-route { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; margin-bottom: 28px; }
.sc-route-city {
    font-size: 28px; font-weight: 900; line-height: 1.1;
    letter-spacing: -0.5px;
}
.sc-route-city.dest { color: rgba(255,255,255,.75); }
.sc-route-arrow {
    display: flex; align-items: center; justify-content: center;
    width: 44px; height: 44px; border-radius: 50%;
    border: 1.5px solid rgba(255,255,255,.15);
    color: #e94f1b; font-size: 16px; flex-shrink: 0;
    background: rgba(255,255,255,.05);
}
.sc-dates { display: flex; gap: 16px; flex-wrap: wrap; }
.sc-date-card {
    padding: 14px 20px; border-radius: 16px; min-width: 160px;
}
.sc-date-depart { background: linear-gradient(135deg, #e94f1b, #f97316); }
.sc-date-retour { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); }
.sc-date-label {
    font-size: 9px; font-weight: 800; letter-spacing: 2px;
    text-transform: uppercase; color: rgba(255,255,255,.55); margin-bottom: 4px;
}
.sc-date-value { font-size: 17px; font-weight: 900; color: #fff; line-height: 1.2; }
.sc-date-time  { font-size: 12px; font-weight: 600; color: rgba(255,255,255,.6); margin-top: 3px; }

/* ── Info Grid (3 cartes) ── */
.sc-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; margin-bottom: 24px; }
@media (max-width: 900px) { .sc-cards { grid-template-columns: 1fr 1fr; } }
@media (max-width: 600px) { .sc-cards { grid-template-columns: 1fr; } }

.sc-card {
    background: #fff; border-radius: 20px;
    border: 1px solid #f1f5f9; padding: 24px;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
    transition: box-shadow .2s, transform .2s;
    overflow: hidden; position: relative;
}
.sc-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.08); transform: translateY(-2px); }
.sc-card-label {
    font-size: 10px; font-weight: 800; letter-spacing: 1.5px;
    text-transform: uppercase; color: #9ca3af; margin-bottom: 16px;
    display: flex; align-items: center; gap: 8px;
}
.sc-card-label i { font-size: 12px; }

/* Demandeur */
.sc-avatar {
    width: 52px; height: 52px; border-radius: 16px;
    background: linear-gradient(135deg, #ff7043, #e94f1b);
    color: #fff; font-size: 20px; font-weight: 900;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; box-shadow: 0 4px 12px rgba(233,79,27,.25);
}
.sc-user-name  { font-size: 16px; font-weight: 900; color: #111827; margin-bottom: 3px; }
.sc-user-email { font-size: 12px; font-weight: 500; color: #6b7280; margin-bottom: 4px; }
.sc-user-phone { font-size: 13px; font-weight: 700; color: #e94f1b; }

/* Passagers */
.sc-big-num { font-size: 52px; font-weight: 900; color: #111827; line-height: 1; letter-spacing: -2px; }
.sc-sub-num { font-size: 13px; font-weight: 700; color: #6b7280; margin-top: 6px; }
.sc-progress-track { background: #f3f4f6; border-radius: 99px; height: 6px; margin-top: 14px; overflow: hidden; }
.sc-progress-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #3b82f6, #6366f1); transition: width 1s ease; }

/* Montant */
.sc-amount { font-size: 36px; font-weight: 900; color: #059669; letter-spacing: -1px; line-height: 1; }
.sc-amount-unit { font-size: 13px; font-weight: 800; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }
.sc-amount-empty { display: flex; align-items: center; gap: 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 14px; padding: 16px; }
.sc-amount-empty p { font-size: 13px; font-weight: 700; color: #92400e; margin: 0; }
.sc-amount-empty small { font-size: 11px; color: #b45309; }

/* ── Context chips ── */
.sc-chips { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; }
.sc-chip {
    display: flex; align-items: center; gap: 10px;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 14px; padding: 12px 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.sc-chip-icon {
    width: 34px; height: 34px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.sc-chip-label { font-size: 10px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 2px; }
.sc-chip-value { font-size: 13px; font-weight: 800; color: #111827; }
.sc-chip-blue  .sc-chip-icon { background: #eff6ff; color: #2563eb; }
.sc-chip-purple .sc-chip-icon { background: #f5f3ff; color: #7c3aed; }
.sc-chip-gray   .sc-chip-icon { background: #f3f4f6; color: #4b5563; }

/* Alert chips (refus/annulation) */
.sc-banner {
    border-radius: 16px; padding: 18px 22px; margin-bottom: 24px;
    display: flex; align-items: flex-start; gap: 14px;
}
.sc-banner-icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
.sc-banner-title { font-size: 13px; font-weight: 800; margin-bottom: 3px; }
.sc-banner-body  { font-size: 13px; font-weight: 500; line-height: 1.6; }
.sc-banner-warn { background: #fffbeb; border: 1px solid #fde68a; color: #78350f; }
.sc-banner-danger { background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d; }
.sc-banner-info   { background: #f5f3ff; border: 1px solid #ddd6fe; color: #3b0764; }
.sc-banner-note { background: #fffbeb; border: 1px solid #fde68a; color: #78350f; font-size: 12px; font-weight: 600; margin-top: 8px; }

/* ── Action panels ── */
.sc-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
@media (max-width: 768px) { .sc-actions { grid-template-columns: 1fr; } }

.sc-panel {
    background: #fff; border-radius: 20px;
    border: 1px solid #e5e7eb; overflow: hidden;
    box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.sc-panel-head {
    padding: 20px 24px; border-bottom: 1px solid #f3f4f6;
    display: flex; align-items: center; gap: 14px;
}
.sc-panel-head-icon {
    width: 40px; height: 40px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 16px;
}
.sc-panel-title { font-size: 15px; font-weight: 900; color: #111827; margin: 0; }
.sc-panel-sub   { font-size: 12px; font-weight: 500; color: #6b7280; margin-top: 2px; }
.sc-panel-body  { padding: 24px; }

.sc-panel-green { border-color: #a7f3d0; }
.sc-panel-green .sc-panel-head { background: #f0fdf4; border-bottom-color: #bbf7d0; }
.sc-panel-green .sc-panel-head-icon { background: #dcfce7; color: #16a34a; }

.sc-panel-red { border-color: #fecaca; }
.sc-panel-red .sc-panel-head { background: #fef2f2; border-bottom-color: #fecaca; }
.sc-panel-red .sc-panel-head-icon { background: #fee2e2; color: #dc2626; }

/* ── Form elements ── */
.sc-label {
    display: block; font-size: 11px; font-weight: 800;
    color: #374151; text-transform: uppercase; letter-spacing: .8px; margin-bottom: 8px;
}
.sc-input {
    width: 100%; padding: 12px 16px; border-radius: 12px;
    border: 1.5px solid #e5e7eb; font-size: 14px; font-weight: 600; color: #111827;
    background: #f9fafb; outline: none; transition: border-color .2s, background .2s, box-shadow .2s;
}
.sc-input:focus { border-color: #e94f1b; background: #fff; box-shadow: 0 0 0 3px rgba(233,79,27,.1); }
.sc-textarea { resize: vertical; min-height: 90px; }
.sc-select { -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url("data:image/svg+xml,%3Csvg fill='none' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='%236b7280' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 14px center; background-size: 20px; padding-right: 44px; }

/* ── Buttons ── */
.sc-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 13px 24px; border-radius: 12px;
    font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px;
    border: none; cursor: pointer; transition: all .2s; text-decoration: none; width: 100%;
}
.sc-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,0,0,.15); text-decoration: none; }
.sc-btn-green { background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; box-shadow: 0 4px 12px rgba(34,197,94,.25); }
.sc-btn-red   { background: #fff; border: 2px solid #ef4444; color: #dc2626; }
.sc-btn-red:hover { background: #fef2f2; box-shadow: 0 6px 18px rgba(239,68,68,.15); }
.sc-btn-blue  { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; box-shadow: 0 4px 12px rgba(59,130,246,.25); }
.sc-btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; box-shadow: 0 4px 12px rgba(239,68,68,.25); }
.sc-btn:disabled { opacity: .5; cursor: not-allowed; transform: none !important; box-shadow: none !important; }

/* ── GPS Card ── */
.sc-gps {
    background: linear-gradient(135deg, #0f172a, #1e293b);
    border-radius: 20px; padding: 28px 32px; margin-bottom: 24px; color: #fff;
    position: relative; overflow: hidden;
}
.sc-gps-head { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 20px; flex-wrap: wrap; }
.sc-gps-title-wrap { display: flex; align-items: center; gap: 14px; }
.sc-gps-icon { width: 44px; height: 44px; border-radius: 14px; background: rgba(99,102,241,.2); border: 1px solid rgba(99,102,241,.3); display: flex; align-items: center; justify-content: center; font-size: 18px; color: #818cf8; }
.sc-gps-title { font-size: 17px; font-weight: 900; margin: 0; }
.sc-gps-sub   { font-size: 12px; color: rgba(255,255,255,.4); margin-top: 2px; }
.sc-gps-badge { background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); color: rgba(255,255,255,.6); font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .8px; padding: 6px 14px; border-radius: 20px; }
.sc-gps-coords { font-size: 22px; font-weight: 900; color: #a5b4fc; font-family: monospace; letter-spacing: -0.5px; margin-bottom: 8px; }
.sc-gps-meta   { font-size: 12px; color: rgba(255,255,255,.4); font-weight: 500; }
.sc-gps-map-btn {
    display: inline-flex; align-items: center; gap: 8px; margin-top: 14px;
    padding: 10px 20px; border-radius: 12px;
    background: rgba(99,102,241,.25); border: 1px solid rgba(99,102,241,.35);
    color: #a5b4fc; font-size: 12px; font-weight: 800;
    text-decoration: none; transition: background .2s;
}
.sc-gps-map-btn:hover { background: rgba(99,102,241,.4); color: #c7d2fe; text-decoration: none; }

/* ── Passengers Table ── */
.sc-table-card {
    background: #fff; border-radius: 20px;
    border: 1px solid #f1f5f9; overflow: hidden;
    margin-bottom: 24px; box-shadow: 0 1px 4px rgba(0,0,0,.05);
}
.sc-table-head {
    display: flex; align-items: center; gap: 14px;
    padding: 20px 24px; border-bottom: 1px solid #f3f4f6; background: #fff;
}
.sc-table-head-icon { width: 40px; height: 40px; border-radius: 12px; background: #fff7f5; color: #e94f1b; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; }
.sc-table-title { font-size: 16px; font-weight: 900; color: #111827; margin: 0; }
.sc-table-sub   { font-size: 12px; color: #6b7280; margin-top: 2px; }
.sc-table-badge { margin-left: auto; background: #f3f4f6; color: #374151; font-size: 11px; font-weight: 800; padding: 4px 12px; border-radius: 20px; white-space: nowrap; }
.sc-garant-badge { background: #ede9fe; color: #5b21b6; }

table.sc-tbl { width: 100%; border-collapse: collapse; }
table.sc-tbl thead tr { background: #f8fafc; }
table.sc-tbl th {
    padding: 13px 22px; text-align: left; font-size: 10px;
    font-weight: 800; text-transform: uppercase; letter-spacing: 1.2px; color: #9ca3af;
    border-bottom: 1px solid #f1f5f9; white-space: nowrap;
}
table.sc-tbl td { padding: 15px 22px; vertical-align: middle; border-bottom: 1px solid #f9fafb; }
table.sc-tbl tbody tr:last-child td { border-bottom: none; }
table.sc-tbl tbody tr:hover { background: #fafafa; }

.sc-pass-num {
    width: 30px; height: 30px; border-radius: 50%;
    background: #f3f4f6; color: #6b7280; font-size: 12px; font-weight: 800;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s, color .2s;
}
table.sc-tbl tbody tr:hover .sc-pass-num { background: #fff0ec; color: #e94f1b; }
.sc-pass-avatar {
    width: 36px; height: 36px; border-radius: 12px;
    background: linear-gradient(135deg, #e94f1b20, #e94f1b10);
    color: #e94f1b; font-size: 14px; font-weight: 900;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.sc-pass-name   { font-size: 14px; font-weight: 800; color: #111827; }
.sc-pass-prenom { font-size: 12px; font-weight: 500; color: #6b7280; margin-top: 1px; }
.sc-pass-phone-link {
    display: inline-flex; align-items: center; gap: 8px;
    background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 10px;
    padding: 7px 14px; color: #374151; font-size: 13px; font-weight: 700;
    text-decoration: none; transition: all .2s;
}
.sc-pass-phone-link:hover { background: #fff0ec; border-color: #e94f1b; color: #e94f1b; text-decoration: none; }
.sc-pass-phone-link i { font-size: 11px; color: #e94f1b; }
.sc-pass-urgence {
    display: inline-flex; align-items: center; gap: 7px;
    background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px;
    padding: 7px 14px; color: #b91c1c; font-size: 13px; font-weight: 700;
}
.sc-pass-none { color: #d1d5db; font-size: 20px; }

.sc-empty-row td { padding: 60px 24px; text-align: center; }
.sc-empty-icon { width: 64px; height: 64px; border-radius: 50%; background: #f3f4f6; color: #d1d5db; font-size: 26px; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
.sc-empty-text { font-size: 15px; font-weight: 800; color: #374151; margin-bottom: 4px; }
.sc-empty-sub  { font-size: 13px; color: #9ca3af; }

/* ── Danger Zone – Annulation ── */
.sc-danger-zone {
    background: #fff5f5; border-radius: 20px;
    border: 1.5px solid #fecaca; padding: 28px 32px;
}
.sc-danger-header { display: flex; align-items: flex-start; gap: 16px; margin-bottom: 24px; }
.sc-danger-icon {
    width: 48px; height: 48px; border-radius: 16px;
    background: #fee2e2; color: #dc2626; font-size: 20px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.sc-danger-title { font-size: 17px; font-weight: 900; color: #7f1d1d; margin: 0; }
.sc-danger-sub   { font-size: 13px; color: #b91c1c; margin-top: 4px; }
.sc-danger-form { background: #fff; border-radius: 16px; border: 1px solid #fecaca; padding: 22px; }
.sc-danger-footer { display: flex; justify-content: flex-end; margin-top: 16px; }
.sc-btn-inline { width: auto !important; padding: 11px 24px; }

/* ── Gare Panel ── */
.sc-gare-panel {
    background: #fff; border-radius: 20px;
    border: 1px solid #bfdbfe; overflow: hidden;
    margin-bottom: 24px; box-shadow: 0 1px 4px rgba(59,130,246,.08);
}
.sc-gare-head {
    background: #eff6ff; padding: 20px 24px;
    border-bottom: 1px solid #bfdbfe; display: flex; align-items: center; gap: 14px;
}
.sc-gare-icon { width: 40px; height: 40px; border-radius: 12px; background: #dbeafe; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 16px; }
.sc-gare-title { font-size: 16px; font-weight: 900; color: #1e3a8a; margin: 0; }
.sc-gare-sub   { font-size: 12px; color: #3b82f6; margin-top: 2px; }
.sc-gare-body  { padding: 24px; }
.sc-gare-row   { display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap; }
.sc-gare-row .sc-input-wrap { flex: 1; min-width: 200px; }
</style>
@endsection

@section('content')
@php
    $statuses = [
        'en_attente' => 'En attente',
        'valide'     => 'Validé',
        'refuse'     => 'Refusé',
        'paye'       => 'Payé',
        'en_cours'   => 'En cours',
        'termine'    => 'Terminé',
        'annule'     => 'Annulé',
    ];
    $statusIcons = [
        'en_attente' => 'fa-clock',
        'valide'     => 'fa-check',
        'paye'       => 'fa-money-bill-wave',
        'en_cours'   => 'fa-route',
        'termine'    => 'fa-flag-checkered',
        'refuse'     => 'fa-times',
        'annule'     => 'fa-ban',
    ];
    $slabel = $statuses[$convoi->statut] ?? ucfirst($convoi->statut);
    $sicon  = $statusIcons[$convoi->statut] ?? 'fa-info-circle';
    $passagersEnregistres = $convoi->passagers ? $convoi->passagers->count() : 0;
    $passagersComplets    = $convoi->is_garant || ($passagersEnregistres >= $convoi->nombre_personnes);
    $pct = $convoi->nombre_personnes > 0 ? min(100, round($passagersEnregistres / $convoi->nombre_personnes * 100)) : 0;
@endphp

<div class="sc-page">

    {{-- Back --}}
    <a href="{{ route('compagnie.convois.index') }}" class="sc-back">
        <span class="sc-back-icon"><i class="fas fa-arrow-left"></i></span>
        Retour à la liste des convois
    </a>

    {{-- Header --}}
    <div class="sc-header">
        <div>
            <h1 class="sc-title">Détail du <span>Convoi</span></h1>
            <span class="sc-ref">Réf : {{ $convoi->reference }}</span>
        </div>
        <span class="sc-status status-{{ $convoi->statut }}">
            <span class="sc-status-dot"></span>
            <i class="fas {{ $sicon }}"></i>
            {{ $slabel }}
        </span>
    </div>

    {{-- Alertes --}}
    @if(session('success'))
    <div class="sc-alert sc-alert-success">
        <span class="sc-alert-icon">✅</span>
        <div>
            <div class="sc-alert-title">Opération réussie</div>
            <div class="sc-alert-body">{{ session('success') }}</div>
        </div>
    </div>
    @endif
    @if(session('error') || $errors->any())
    <div class="sc-alert sc-alert-error">
        <span class="sc-alert-icon">⚠️</span>
        <div>
            <div class="sc-alert-title">Une erreur est survenue</div>
            @if(session('error'))<div class="sc-alert-body">{{ session('error') }}</div>@endif
            @foreach($errors->all() as $e)<div class="sc-alert-body">• {{ $e }}</div>@endforeach
        </div>
    </div>
    @endif

    {{-- ═══ HERO ITINÉRAIRE ═══ --}}
    <div class="sc-hero">
        <div class="sc-hero-inner">
            <div class="sc-hero-label">
                <i class="fas fa-route"></i>
                Itinéraire du convoi
            </div>
            <div class="sc-route">
                <div class="sc-route-city">{{ $convoi->lieu_depart ?? ($convoi->itineraire->point_depart ?? '—') }}</div>
                <div class="sc-route-arrow"><i class="fas fa-arrow-right"></i></div>
                <div class="sc-route-city dest">{{ $convoi->lieu_retour ?? ($convoi->itineraire->point_arrive ?? '—') }}</div>
            </div>
            <div class="sc-dates">
                <div class="sc-date-card sc-date-depart">
                    <div class="sc-date-label">Date de départ</div>
                    <div class="sc-date-value">{{ $convoi->date_depart ? \Carbon\Carbon::parse($convoi->date_depart)->format('d M Y') : '—' }}</div>
                    @if($convoi->heure_depart)
                    <div class="sc-date-time"><i class="fas fa-clock" style="font-size:10px;margin-right:4px"></i>{{ $convoi->heure_depart }}</div>
                    @endif
                </div>
                @if($convoi->date_retour)
                <div class="sc-date-card sc-date-retour">
                    <div class="sc-date-label">Date de retour</div>
                    <div class="sc-date-value">{{ \Carbon\Carbon::parse($convoi->date_retour)->format('d M Y') }}</div>
                    @if($convoi->heure_retour)
                    <div class="sc-date-time"><i class="fas fa-clock" style="font-size:10px;margin-right:4px"></i>{{ $convoi->heure_retour }}</div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ 3 CARTES INFO ═══ --}}
    <div class="sc-cards">
        {{-- Demandeur --}}
        <div class="sc-card">
            <div class="sc-card-label">
                <i class="fas fa-id-card"></i>
                @if($convoi->created_by_gare) Client sur place @else Demandeur @endif
            </div>
            <div style="display:flex;align-items:center;gap:16px">
                <div class="sc-avatar">{{ strtoupper(substr($convoi->demandeur_nom, 0, 1) ?: 'C') }}</div>
                <div style="min-width:0">
                    <div class="sc-user-name" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        {{ $convoi->demandeur_nom }}
                    </div>
                    @if($convoi->created_by_gare)
                        @if($convoi->client_email)
                        <div class="sc-user-email" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $convoi->client_email }}</div>
                        @endif
                        @if($convoi->client_contact)
                        <div class="sc-user-phone"><i class="fas fa-phone-alt" style="font-size:10px;margin-right:5px"></i>{{ $convoi->client_contact }}</div>
                        @endif
                        <div style="margin-top:6px;">
                            <span style="display:inline-flex;align-items:center;gap:4px;font-size:9px;font-weight:900;background:#fff7ed;color:#ea580c;padding:3px 8px;border-radius:6px;text-transform:uppercase;letter-spacing:0.4px;">
                                <i class="fas fa-store"></i> Créé en gare
                            </span>
                        </div>
                    @else
                        @if($convoi->user->email ?? null)
                        <div class="sc-user-email" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $convoi->user->email }}</div>
                        @endif
                        @if($convoi->user->contact ?? null)
                        <div class="sc-user-phone"><i class="fas fa-phone-alt" style="font-size:10px;margin-right:5px"></i>{{ $convoi->user->contact }}</div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Passagers --}}
        <div class="sc-card">
            <div class="sc-card-label"><i class="fas fa-users"></i> Passagers</div>
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px">
                <div>
                    <div class="sc-big-num">{{ $convoi->nombre_personnes }}</div>
                    <div class="sc-sub-num">places demandées</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:28px;font-weight:900;color:#3b82f6;line-height:1">{{ $passagersEnregistres }}</div>
                    <div style="font-size:11px;font-weight:700;color:#93c5fd;text-transform:uppercase;letter-spacing:.5px">inscrits</div>
                </div>
            </div>
            <div class="sc-progress-track">
                <div class="sc-progress-fill" style="width:{{ $pct }}%"></div>
            </div>
        </div>

        {{-- Montant --}}
        <div class="sc-card">
            <div class="sc-card-label"><i class="fas fa-coins"></i> Montant</div>
            @if($convoi->montant)
            <div class="sc-amount">{{ number_format($convoi->montant, 0, ',', ' ') }}</div>
            <div class="sc-amount-unit">Francs CFA</div>
            @else
            <div class="sc-amount-empty">
                <span style="font-size:22px;color:#f59e0b">💰</span>
                <div>
                    <p>Non défini</p>
                    <small>En attente de validation par la compagnie</small>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══ CHIPS CONTEXTUELS ═══ --}}
    @if($convoi->lieu_rassemblement || $convoi->gare || $convoi->chauffeur || $convoi->vehicule || $convoi->is_garant)
    <div class="sc-chips">
        @if($convoi->lieu_rassemblement)
        <div class="sc-chip sc-chip-blue">
            <div class="sc-chip-icon"><i class="fas fa-map-pin"></i></div>
            <div>
                <div class="sc-chip-label">Rassemblement</div>
                <div class="sc-chip-value">{{ $convoi->lieu_rassemblement }}</div>
            </div>
        </div>
        @endif
        @if($convoi->is_garant)
        <div class="sc-chip sc-chip-purple">
            <div class="sc-chip-icon"><i class="fas fa-user-shield"></i></div>
            <div>
                <div class="sc-chip-label">Mode</div>
                <div class="sc-chip-value">Garant désigné</div>
            </div>
        </div>
        @endif
        @if($convoi->gare)
        <div class="sc-chip sc-chip-gray">
            <div class="sc-chip-icon"><i class="fas fa-warehouse"></i></div>
            <div>
                <div class="sc-chip-label">Gare assignée</div>
                <div class="sc-chip-value">{{ $convoi->gare->nom_gare }}</div>
            </div>
        </div>
        @endif
        @if($convoi->vehicule)
        <div class="sc-chip sc-chip-gray">
            <div class="sc-chip-icon"><i class="fas fa-bus"></i></div>
            <div>
                <div class="sc-chip-label">Véhicule</div>
                <div class="sc-chip-value">{{ $convoi->vehicule->immatriculation }} &middot; {{ $convoi->vehicule->nombre_place ?? '?' }} pl.</div>
            </div>
        </div>
        @endif
        @if($convoi->chauffeur)
        <div class="sc-chip sc-chip-gray">
            <div class="sc-chip-icon"><i class="fas fa-user-tie"></i></div>
            <div>
                <div class="sc-chip-label">Chauffeur</div>
                <div class="sc-chip-value">{{ trim(($convoi->chauffeur->prenom ?? '') . ' ' . ($convoi->chauffeur->name ?? '')) }}</div>
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Motifs refus/annulation --}}
    @if($convoi->motif_refus)
    <div class="sc-banner sc-banner-danger">
        <span class="sc-banner-icon">🚫</span>
        <div>
            <div class="sc-banner-title">Motif de refus</div>
            <div class="sc-banner-body">{{ $convoi->motif_refus }}</div>
        </div>
    </div>
    @endif
    @if($convoi->motif_annulation_chauffeur)
    <div class="sc-banner sc-banner-warn">
        <span class="sc-banner-icon">⚠️</span>
        <div>
            <div class="sc-banner-title">Désistement du chauffeur</div>
            <div class="sc-banner-body">{{ $convoi->motif_annulation_chauffeur }}</div>
            <div class="sc-banner-note">📋 La gare doit réaffecter un chauffeur et un véhicule de remplacement.</div>
        </div>
    </div>
    @endif

    {{-- ═══ INFO : Traitement géré par la gare ═══ --}}
    @if (in_array($convoi->statut, ['en_attente', 'valide', 'refuse']))
    <div class="sc-banner sc-banner-info" style="margin-bottom:24px;">
        <span class="sc-banner-icon">🏢</span>
        <div>
            <div class="sc-banner-title">Traitement délégué à la gare</div>
            <div class="sc-banner-body">
                @if($convoi->gare)
                    La gare <strong>{{ $convoi->gare->nom_gare }}</strong> prend en charge la validation et la gestion de ce convoi.
                @else
                    Ce convoi est en attente d'une gare pour traitement.
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ TRACKING GPS ═══ --}}
    @if(in_array($convoi->statut, ['en_cours', 'termine']))
    <div class="sc-gps">
        <div class="sc-gps-head">
            <div class="sc-gps-title-wrap">
                <div class="sc-gps-icon"><i class="fas fa-satellite-dish"></i></div>
                <div>
                    <div class="sc-gps-title">Suivi GPS — Temps Réel</div>
                    <div class="sc-gps-sub">Position du véhicule du convoi</div>
                </div>
            </div>
            <span class="sc-gps-badge" id="trackingStatusBadge">—</span>
        </div>
        <div>
            <div class="sc-gps-coords" id="trackingCoords">Recherche du signal GPS...</div>
            <div class="sc-gps-meta" id="trackingMeta">En attente de connexion avec l'application du chauffeur.</div>
            <a href="#" id="trackingMapLink" target="_blank" class="sc-gps-map-btn" style="display:none">
                <i class="fas fa-map-marked-alt"></i> Voir sur Google Maps
            </a>
        </div>
    </div>
    @endif

    {{-- ═══ MANIFESTE PASSAGERS ═══ --}}
    <div class="sc-table-card">
        <div class="sc-table-head">
            <div class="sc-table-head-icon"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="sc-table-title">Manifeste des Passagers</div>
                <div class="sc-table-sub">{{ $passagersEnregistres }} enregistré(s) sur {{ $convoi->nombre_personnes }} places</div>
            </div>
            <div class="sc-table-badge {{ $convoi->is_garant ? 'sc-garant-badge' : '' }}">
                @if($convoi->is_garant)<i class="fas fa-shield-alt" style="margin-right:5px"></i>Garant @endif
                {{ $passagersEnregistres }} / {{ $convoi->nombre_personnes }}
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="sc-tbl">
                <thead>
                    <tr>
                        <th style="width:60px;text-align:center">#</th>
                        <th>Passager</th>
                        <th>Contact</th>
                        <th>Urgence</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($convoi->passagers as $i => $passager)
                    <tr>
                        <td style="text-align:center">
                            <div class="sc-pass-num" style="margin:0 auto">{{ $i + 1 }}</div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="sc-pass-avatar">{{ strtoupper(substr($passager->nom ?? '?', 0, 1)) }}</div>
                                <div>
                                    <div class="sc-pass-name">{{ $passager->nom }}</div>
                                    <div class="sc-pass-prenom">{{ $passager->prenoms }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($passager->contact)
                            <a href="tel:{{ $passager->contact }}" class="sc-pass-phone-link">
                                <i class="fas fa-phone-alt"></i> {{ $passager->contact }}
                            </a>
                            @else
                            <span class="sc-pass-none">—</span>
                            @endif
                        </td>
                        <td>
                            @if($passager->contact_urgence)
                            <div class="sc-pass-urgence">
                                <i class="fas fa-heartbeat" style="font-size:11px"></i> {{ $passager->contact_urgence }}
                            </div>
                            @else
                            <span class="sc-pass-none">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr class="sc-empty-row">
                        <td colspan="4">
                            <div class="sc-empty-icon"><i class="fas fa-user-slash"></i></div>
                            <div class="sc-empty-text">Aucun passager enregistré</div>
                            <div class="sc-empty-sub">
                                @if($convoi->is_garant)
                                Le demandeur administre le groupe en tant que garant.
                                @else
                                L'utilisateur n'a pas encore renseigné la liste.
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Annulation supprimée — la gare gère les convois --}}

</div>
@endsection

@section('scripts')
@if(in_array($convoi->statut, ['en_cours', 'termine']))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const endpoint  = "{{ route('compagnie.convois.location', $convoi->id) }}";
    const coordsEl  = document.getElementById('trackingCoords');
    const metaEl    = document.getElementById('trackingMeta');
    const badgeEl   = document.getElementById('trackingStatusBadge');
    const mapLinkEl = document.getElementById('trackingMapLink');

    function updateTracking() {
        fetch(endpoint, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;
                badgeEl.textContent = (data.statut || '--').replace(/_/g,' ');
                if (data.latitude !== null && data.longitude !== null) {
                    coordsEl.textContent = data.latitude + ', ' + data.longitude;
                    metaEl.textContent   = 'MàJ : ' + data.last_update + '  ·  Chauffeur : ' + data.chauffeur + '  ·  Véhicule : ' + data.vehicule;
                    mapLinkEl.href       = 'https://www.google.com/maps?q=' + data.latitude + ',' + data.longitude;
                    mapLinkEl.style.display = 'inline-flex';
                } else {
                    coordsEl.textContent = 'Signal non disponible';
                    metaEl.textContent   = 'Dernière vérification : ' + data.last_update;
                    mapLinkEl.style.display = 'none';
                }
            }).catch(function(){});
    }

    updateTracking();
    setInterval(updateTracking, 10000);
});
</script>
@endif
@endsection
