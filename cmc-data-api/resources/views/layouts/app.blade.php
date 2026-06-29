<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CMC') — Tableau de bord</title>

    {{-- Google Fonts: Inter (body) + Space Grotesk (headings) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════════════
           DESIGN TOKENS
        ═══════════════════════════════════════════════════ */
        :root {
            --navy:       #1B2B4B;
            --navy-light: #243659;
            --navy-muted: #334F7C;
            --indigo:     #4F46E5;
            --indigo-dim: #3730A3;
            --indigo-soft:#EEF2FF;
            --amber:      #F59E0B;
            --amber-soft: #FEF3C7;
            --green:      #10B981;
            --green-soft: #D1FAE5;
            --red:        #EF4444;
            --red-soft:   #FEE2E2;
            --slate-50:   #F8FAFC;
            --slate-100:  #F1F5F9;
            --slate-200:  #E2E8F0;
            --slate-400:  #94A3B8;
            --slate-500:  #64748B;
            --slate-700:  #334155;
            --slate-900:  #0F172A;
            --warm-white: #F8F7F4;
            --sidebar-w:  260px;
        }

        /* ═══════════════════════════════════════════════════
           RESET & BASE
        ═══════════════════════════════════════════════════ */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--slate-700);
            background: var(--warm-white);
            display: flex;
        }

        a { color: inherit; text-decoration: none; }

        /* ═══════════════════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: var(--navy);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            overflow-y: auto;
        }

        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .sidebar-logo .brand {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 20px;
            color: #fff;
            letter-spacing: -.3px;
        }

        .sidebar-logo .brand span {
            color: var(--amber);
        }

        .sidebar-logo .subtitle {
            font-size: 11px;
            color: var(--slate-400);
            margin-top: 2px;
            letter-spacing: .4px;
            text-transform: uppercase;
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 0;
        }

        .nav-section {
            padding: 16px 16px 4px;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--navy-muted);
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 16px;
            color: rgba(255,255,255,.65);
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 0;
            transition: background .15s, color .15s;
            position: relative;
        }

        .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: #fff;
        }

        .nav-link.active {
            background: rgba(79,70,229,.18);
            color: #fff;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: var(--indigo);
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            width: 18px;
            height: 18px;
            opacity: .8;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: rgba(79,70,229,.35);
            color: #a5b4fc;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 20px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        .api-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(255,255,255,.06);
            border-radius: 8px;
            color: rgba(255,255,255,.5);
            font-size: 12px;
            font-weight: 500;
            transition: background .15s, color .15s;
        }

        .api-link:hover {
            background: rgba(255,255,255,.1);
            color: rgba(255,255,255,.8);
        }

        /* ═══════════════════════════════════════════════════
           MAIN CONTENT
        ═══════════════════════════════════════════════════ */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ─── Top bar ─── */
        .topbar {
            height: 60px;
            background: #fff;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            padding: 0 32px;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 40;
        }

        .topbar-breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--slate-500);
        }

        .topbar-breadcrumb .current {
            font-weight: 600;
            color: var(--slate-700);
        }

        .topbar-sep {
            color: var(--slate-400);
        }

        .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ─── Page body ─── */
        .page-body {
            padding: 32px;
            flex: 1;
        }

        /* ═══════════════════════════════════════════════════
           PAGE HEADER
        ═══════════════════════════════════════════════════ */
        .page-header {
            margin-bottom: 28px;
            display: flex;
            align-items: flex-end;
            gap: 16px;
        }

        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: -.4px;
            line-height: 1.2;
        }

        .page-subtitle {
            font-size: 13px;
            color: var(--slate-500);
            margin-top: 4px;
            font-weight: 400;
        }

        .page-header-actions {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }

        /* ═══════════════════════════════════════════════════
           BUTTONS
        ═══════════════════════════════════════════════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--indigo);
            color: #fff;
        }

        .btn-primary:hover { background: var(--indigo-dim); }

        .btn-outline {
            background: #fff;
            color: var(--slate-700);
            border: 1.5px solid var(--slate-200);
        }

        .btn-outline:hover {
            border-color: var(--slate-400);
            background: var(--slate-50);
        }

        .btn-danger {
            background: var(--red);
            color: #fff;
        }

        .btn-danger:hover { background: #dc2626; }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* ═══════════════════════════════════════════════════
           CARDS
        ═══════════════════════════════════════════════════ */
        .card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--navy);
        }

        .card-body {
            padding: 24px;
        }

        /* ═══════════════════════════════════════════════════
           STATS GRID
        ═══════════════════════════════════════════════════ */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            padding: 20px 22px;
            position: relative;
            overflow: hidden;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 4px;
            height: 100%;
            background: var(--stat-color, var(--indigo));
            border-radius: 0 12px 12px 0;
        }

        .stat-label {
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--slate-500);
            margin-bottom: 8px;
        }

        .stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--navy);
            line-height: 1;
        }

        .stat-sub {
            font-size: 11.5px;
            color: var(--slate-400);
            margin-top: 6px;
        }

        /* ═══════════════════════════════════════════════════
           FILTER BAR
        ═══════════════════════════════════════════════════ */
        .filter-bar {
            background: #fff;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            padding: 14px 20px;
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
            align-items: flex-end;
            flex-wrap: wrap;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-label {
            font-size: 11px;
            font-weight: 600;
            color: var(--slate-500);
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .filter-input, .filter-select {
            height: 36px;
            padding: 0 12px;
            border: 1.5px solid var(--slate-200);
            border-radius: 7px;
            font-size: 13px;
            color: var(--slate-700);
            background: var(--slate-50);
            font-family: inherit;
            transition: border-color .15s;
            min-width: 140px;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: var(--indigo);
            background: #fff;
        }

        .filter-input::placeholder { color: var(--slate-400); }

        .filter-actions {
            display: flex;
            gap: 8px;
            margin-left: auto;
        }

        /* ═══════════════════════════════════════════════════
           TABLE
        ═══════════════════════════════════════════════════ */
        .table-wrap {
            background: #fff;
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            padding: 12px 18px;
            text-align: left;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--slate-500);
            background: var(--slate-50);
            border-bottom: 1px solid var(--slate-200);
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid var(--slate-100);
            transition: background .12s;
        }

        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fafbff; }

        tbody td {
            padding: 13px 18px;
            font-size: 13.5px;
            color: var(--slate-700);
            vertical-align: middle;
        }

        .table-link {
            font-weight: 600;
            color: var(--navy);
            transition: color .12s;
        }

        .table-link:hover { color: var(--indigo); }

        /* ═══════════════════════════════════════════════════
           BADGES / CHIPS
        ═══════════════════════════════════════════════════ */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .2px;
        }

        .badge-indigo  { background: var(--indigo-soft); color: var(--indigo-dim); }
        .badge-green   { background: var(--green-soft);  color: #065F46; }
        .badge-amber   { background: var(--amber-soft);  color: #92400E; }
        .badge-red     { background: var(--red-soft);    color: #991B1B; }
        .badge-gray    { background: var(--slate-100);   color: var(--slate-500); }
        .badge-navy    { background: #E3E8F5; color: var(--navy); }

        /* ═══════════════════════════════════════════════════
           PAGINATION
        ═══════════════════════════════════════════════════ */
        .pagination-wrap {
            padding: 16px 24px;
            border-top: 1px solid var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
        }

        .pagination-info {
            font-size: 12.5px;
            color: var(--slate-500);
        }

        .pagination-links {
            display: flex;
            gap: 4px;
        }

        .pagination-links a,
        .pagination-links span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 7px;
            font-size: 13px;
            font-weight: 500;
            border: 1.5px solid var(--slate-200);
            color: var(--slate-600);
            transition: all .12s;
        }

        .pagination-links a:hover { border-color: var(--indigo); color: var(--indigo); }

        .pagination-links .active span,
        .pagination-links span[aria-current="page"] {
            background: var(--indigo);
            color: #fff;
            border-color: var(--indigo);
        }

        /* ═══════════════════════════════════════════════════
           DETAIL PANELS
        ═══════════════════════════════════════════════════ */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .detail-grid.thirds {
            grid-template-columns: repeat(3, 1fr);
        }

        .detail-field { }

        .detail-field-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--slate-400);
            margin-bottom: 4px;
        }

        .detail-field-value {
            font-size: 14.5px;
            color: var(--slate-700);
            font-weight: 500;
        }

        .detail-field-value.mono {
            font-family: 'SFMono-Regular', Consolas, monospace;
            font-size: 13px;
            background: var(--slate-100);
            padding: 3px 8px;
            border-radius: 5px;
            display: inline-block;
        }

        /* ═══════════════════════════════════════════════════
           ALERTS / FLASH
        ═══════════════════════════════════════════════════ */
        .alert {
            padding: 12px 18px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-size: 13.5px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success { background: var(--green-soft); color: #065F46; border: 1px solid #A7F3D0; }
        .alert-error   { background: var(--red-soft);   color: #991B1B; border: 1px solid #FECACA; }
        .alert-warning { background: var(--amber-soft); color: #92400E; border: 1px solid #FDE68A; }

        /* ═══════════════════════════════════════════════════
           FORMS
        ═══════════════════════════════════════════════════ */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full { grid-column: 1 / -1; }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--slate-600);
            letter-spacing: .2px;
        }

        .form-label .req { color: var(--red); margin-left: 2px; }

        .form-control {
            height: 40px;
            padding: 0 14px;
            border: 1.5px solid var(--slate-200);
            border-radius: 8px;
            font-size: 13.5px;
            color: var(--slate-700);
            font-family: inherit;
            background: #fff;
            transition: border-color .15s, box-shadow .15s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--indigo);
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }

        select.form-control { cursor: pointer; }

        .form-error {
            font-size: 12px;
            color: var(--red);
            font-weight: 500;
        }

        .form-actions {
            margin-top: 28px;
            display: flex;
            gap: 12px;
        }

        /* ═══════════════════════════════════════════════════
           EMPTY STATE
        ═══════════════════════════════════════════════════ */
        .empty-state {
            padding: 64px 32px;
            text-align: center;
        }

        .empty-icon {
            font-size: 40px;
            margin-bottom: 12px;
            opacity: .35;
        }

        .empty-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 17px;
            font-weight: 600;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .empty-sub {
            font-size: 13px;
            color: var(--slate-400);
        }

        /* ═══════════════════════════════════════════════════
           MISC UTILITIES
        ═══════════════════════════════════════════════════ */
        .text-muted { color: var(--slate-400); }
        .text-sm    { font-size: 12px; }
        .font-mono  { font-family: 'SFMono-Regular', Consolas, monospace; }
        .mt-24 { margin-top: 24px; }
        .mb-4  { margin-bottom: 4px; }

        /* ─── Score bar (for notes) ─── */
        .score-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .score-track {
            flex: 1;
            height: 5px;
            background: var(--slate-200);
            border-radius: 99px;
            overflow: hidden;
        }

        .score-fill {
            height: 100%;
            border-radius: 99px;
            background: var(--indigo);
            transition: width .3s;
        }

        .score-fill.pass  { background: var(--green); }
        .score-fill.fail  { background: var(--red); }

        /* ─── Avatar circle ─── */
        .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--indigo-soft);
            color: var(--indigo-dim);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .5px;
            flex-shrink: 0;
        }

        /* ─── Responsive ─── */
        @media (max-width: 900px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrap { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .detail-grid, .form-grid { grid-template-columns: 1fr; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ═══════════ SIDEBAR ═══════════ --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="brand">CMC<span>·</span>Data</div>
        <div class="subtitle">Gestion de formation</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Vue d'ensemble</div>
        <a href="{{ route('web.dashboard') }}"
           class="nav-link {{ request()->routeIs('web.dashboard') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Tableau de bord
        </a>

        <div class="nav-section">Structure</div>
        <a href="{{ route('web.poles.index') }}"
           class="nav-link {{ request()->routeIs('web.poles.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Pôles
        </a>
        <a href="{{ route('web.filieres.index') }}"
           class="nav-link {{ request()->routeIs('web.filieres.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Filières
        </a>
        <a href="{{ route('web.groupes.index') }}"
           class="nav-link {{ request()->routeIs('web.groupes.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Groupes
        </a>
        <a href="{{ route('web.modules.index') }}"
           class="nav-link {{ request()->routeIs('web.modules.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Modules
        </a>
        <a href="{{ route('web.annees.index') }}"
           class="nav-link {{ request()->routeIs('web.annees.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Années
        </a>
        <a href="{{ route('web.niveaux.index') }}"
           class="nav-link {{ request()->routeIs('web.niveaux.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.669 0-3.218.51-4.5 1.385A7.962 7.962 0 009 4.804z"/>
            </svg>
            Niveaux
        </a>
        <a href="{{ route('web.type-formations.index') }}"
           class="nav-link {{ request()->routeIs('web.type-formations.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
            Types formation
        </a>
        <a href="{{ route('web.espaces.index') }}"
           class="nav-link {{ request()->routeIs('web.espaces.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Espaces
        </a>

        <div class="nav-section">Personnes</div>
        <a href="{{ route('web.formateurs.index') }}"
           class="nav-link {{ request()->routeIs('web.formateurs.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Formateurs
        </a>
        <a href="{{ route('web.stagiaires.index') }}"
           class="nav-link {{ request()->routeIs('web.stagiaires.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Stagiaires
        </a>

        <div class="nav-section">Planification</div>
        <a href="{{ route('web.affectations.index') }}"
           class="nav-link {{ request()->routeIs('web.affectations.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Affectations
        </a>
        <a href="{{ route('web.seances.index') }}"
           class="nav-link {{ request()->routeIs('web.seances.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Séances
        </a>
        <a href="{{ route('web.time-ranges.index') }}"
           class="nav-link {{ request()->routeIs('web.time-ranges.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Créneaux horaires
        </a>
        <a href="{{ route('web.notes.index') }}"
           class="nav-link {{ request()->routeIs('web.notes.*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Notes
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="/api/v1/poles" class="api-link" target="_blank">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            API REST v1
        </a>
    </div>
</aside>

{{-- ═══════════ MAIN ═══════════ --}}
<div class="main-wrap">
    <header class="topbar">
        <nav class="topbar-breadcrumb">
            <a href="{{ route('web.dashboard') }}">Accueil</a>
            @hasSection('breadcrumb')
                <span class="topbar-sep">/</span>
                @yield('breadcrumb')
            @endif
        </nav>
        <div class="topbar-right">
            @yield('topbar-actions')
        </div>
    </header>

    <main class="page-body">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
/**
 * CMCCascade — shared utility for non-strict cascading filter selects.
 * All index views push their cascade logic via @push('scripts') and call
 * these helpers so the pattern stays DRY and consistent.
 */
window.CMCCascade = (function () {

    /**
     * Reset a <select> to its empty placeholder option only.
     * @param {HTMLSelectElement} select
     * @param {string}            placeholder  Text of the first <option value="">
     */
    function reset(select, placeholder) {
        const text = placeholder || (select.options[0] ? select.options[0].text : '—');
        select.innerHTML = `<option value="">${text}</option>`;
    }

    /**
     * Populate a <select> with items fetched from the API.
     * Restores the previously-selected value when the same item still exists.
     *
     * @param {HTMLSelectElement} select
     * @param {Array}   items       Array of objects from the API
     * @param {string}  valueKey    Property to use as <option value>
     * @param {string}  labelKey    Property to use as <option> text
     * @param {string}  placeholder Text of the empty first option
     * @param {string}  selected    Value to pre-select (e.g. old filter value)
     */
    function populate(select, items, valueKey, labelKey, placeholder, selected) {
        const prev     = selected !== undefined ? String(selected) : String(select.value);
        const ph       = placeholder || (select.options[0] ? select.options[0].text : '—');
        let   html     = `<option value="">${ph}</option>`;

        items.forEach(function (item) {
            const val  = item[valueKey];
            const lbl  = item[labelKey] || val;
            const sel  = String(val) === prev ? ' selected' : '';
            html      += `<option value="${val}"${sel}>${lbl}</option>`;
        });

        select.innerHTML = html;
    }

    /**
     * Fetch JSON from a URL (returns a Promise<Array>).
     * @param {string} url
     */
    function json(url) {
        return fetch(url).then(function (r) {
            if (!r.ok) return [];
            return r.json();
        }).catch(function () { return []; });
    }

    /**
     * Build a query-string URL, omitting blank/null params.
     * @param {string} base
     * @param {Object} params
     */
    function url(base, params) {
        const q = Object.entries(params)
            .filter(function (_ref) { return _ref[1] !== '' && _ref[1] !== null && _ref[1] !== undefined; })
            .map(function (_ref2) { return encodeURIComponent(_ref2[0]) + '=' + encodeURIComponent(_ref2[1]); })
            .join('&');
        return q ? base + '?' + q : base;
    }

    return { reset: reset, populate: populate, json: json, url: url };
})();
</script>

@stack('scripts')
</body>
</html>
