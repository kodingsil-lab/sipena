<?php
$appName = trim((string) app_setting('nama_aplikasi', 'SIPENA'));
if ($appName === '') {
    $appName = 'SIPENA';
}

$publicMode = (bool) ($publicMode ?? false);
$brandTitle = $appName;
$themePrimary = $publicMode ? app_public_theme_color() : app_theme_color();
$themeDark = app_color_shade($themePrimary, -14);
$themeSoft = app_color_shade($themePrimary, 88);
$footerText = trim((string) app_setting('footer_text', ''));
if ($footerText === '') {
    $footerText = $appName . ' - Sistem Informasi Penjaminan Mutu Internal';
}
$publicActiveMenu = strtolower(trim((string) ($publicActiveMenu ?? 'dashboard')));
$isLoggedIn = (bool) session()->get('is_logged_in');

$roleRaw = strtolower(trim((string) session('role')));
$isAdminRole = $roleRaw === 'admin';
$jabatanSession = trim((string) session('jabatan'));
$jabatanLabel = $jabatanSession !== '' ? $jabatanSession : match ($roleRaw) {
    'admin' => 'Admin',
    'kepala_lpm' => 'Kepala LPM',
    default => 'Pengguna',
};
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? $appName); ?></title>
    <?php $appFavicon = app_favicon_url(); ?>
    <?php if ($appFavicon !== ''): ?>
        <link rel="icon" type="image/png" href="<?= esc($appFavicon); ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: <?= esc($themePrimary); ?>;
            --primary-dark: <?= esc($themeDark); ?>;
            --primary-soft: <?= esc($themeSoft); ?>;
            --bg-page: #f4f7fc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-soft: #e8eef8;
        }

        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: var(--bg-page);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        html {
            overflow-x: hidden;
        }

        a {
            text-decoration: none;
        }

        .top-shell {
            z-index: 1030;
            animation: shellDrop .35s ease-out;
        }

        .top-shell.public-shell.fixed-top {
            top: 10px;
        }

        .top-shell.public-shell {
            left: 0;
            right: 0;
            margin-left: auto;
            margin-right: auto;
            max-width: 1280px;
            width: 100%;
        }

        .top-shell.public-shell .topbar-main {
            border-top-left-radius: 18px;
            border-top-right-radius: 18px;
        }

        .top-shell.public-shell .menu-bar {
            border-bottom-left-radius: 16px;
            border-bottom-right-radius: 16px;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
        }

        .topbar-main {
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff;
            padding: 12px 0;
            box-shadow: 0 8px 22px rgba(52, 104, 203, 0.16);
        }

        .topbar-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            min-height: 64px;
        }

        .topbar-inner > * {
            min-width: 0;
        }

        .public-shell-container {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            width: 100%;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            min-width: 0;
            flex: 1 1 auto;
        }

        .brand-logo {
            width: 50px;
            height: 50px;
            border-radius: 0;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: #fff;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
            min-width: 0;
        }

        .brand-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: .2px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .brand-subtitle {
            font-size: 0.88rem;
            font-weight: 500;
            color: rgba(255,255,255,0.92);
            letter-spacing: 0.1px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .profile-dropdown-shell .profile-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.13);
            border: 1px solid rgba(255,255,255,0.18);
            border-radius: 999px;
            padding: 6px 12px 6px 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
            color: #fff;
            transition: all .2s ease;
        }

        .profile-dropdown-shell .profile-toggle:hover,
        .profile-dropdown-shell .profile-toggle.show {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffffff, #dbeafe);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            font-size: 0.88rem;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .profile-meta {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
            text-align: left;
        }

        .profile-name {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
        }

        .profile-role {
            font-size: 12px;
            color: rgba(255,255,255,0.80);
            font-weight: 500;
        }

        .profile-caret {
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.90);
        }

        .profile-dropdown {
            min-width: 220px;
            border-radius: 14px;
            padding: 8px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
        }

        .profile-dropdown .dropdown-item {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 9px 11px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
        }

        .profile-dropdown .dropdown-item.logout-item {
            color: #dc2626;
        }

        .menu-bar {
            background: rgba(255, 255, 255, 0.96);
            border-bottom: 1px solid #dbe6f3;
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.05);
            backdrop-filter: blur(6px);
            padding-top: 7px;
            padding-bottom: 7px;
        }

        .menu-bar .navbar-nav {
            gap: 4px;
        }

        .menu-bar .navbar-toggler {
            border: 0;
            box-shadow: none;
        }

        .menu-link,
        .menu-text {
            color: #1f2937 !important;
            font-weight: 500;
        }

        .menu-link {
            border-radius: 10px;
            padding: 9px 12px !important;
            transition: all .2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            border: 1px solid transparent;
        }

        .menu-link:focus-visible {
            background: #f1f5f9;
            border-color: #e2e8f0;
            color: #1f2937 !important;
            outline: none;
        }

        .menu-link:hover,
        .menu-link.active,
        .menu-link.dropdown-toggle.show,
        .navbar .nav-item.show > .menu-link {
            background: #f1f5f9;
            border-color: #e2e8f0;
            color: #1f2937 !important;
        }

        .menu-icon {
            color: #64748b;
            font-size: 0.95rem;
        }

        .menu-link:hover .menu-icon,
        .menu-link.active .menu-icon,
        .menu-link.dropdown-toggle.show .menu-icon {
            color: #475569;
        }

        .menu-bar .dropdown-menu {
            border: 1px solid var(--border-soft);
            border-radius: 14px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
            padding: 8px;
        }

        .menu-bar .dropdown-item {
            border-radius: 10px;
            padding: 9px 11px;
            font-weight: 400;
        }

        .menu-bar .dropdown-item:hover {
            background: #f3f6fb;
            color: var(--primary);
        }

        .page-wrap {
            padding: 172px 0 28px;
            flex: 1 0 auto;
            animation: pageFade .35s ease-out;
        }

        .content-wrapper {
            width: 100%;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .public-content-wrapper {
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            padding-left: 0;
            padding-right: 0;
            --bs-gutter-x: 0;
        }

        .page-header {
            margin-bottom: 18px;
            background: #ffffff;
            border: 1px solid #dbe6f3;
            border-radius: 14px;
            padding: 16px 18px 14px;
            position: relative;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 4px;
            color: var(--text-main);
        }

        .page-subtitle {
            color: var(--text-muted);
            margin-bottom: 0;
        }

        .page-header::after {
            content: "";
            display: block;
            flex-basis: 100%;
            width: 100%;
            margin-top: 12px;
            border-bottom: 1px dashed #cfd8e3;
        }

        .public-title-hero {
            position: relative;
            border-radius: 16px;
            padding: 18px 20px 16px;
            margin-bottom: 14px;
            background: #ffffff;
            border: 1px solid #dbe6f3;
            overflow: hidden;
        }

        .public-title-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .public-title-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 10px;
            border-radius: 999px;
            border: 1px solid #c9daf8;
            background: #ffffff;
            color: #2f6adf;
            font-size: .74rem;
            font-weight: 700;
            letter-spacing: .03em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .public-title-main {
            margin: 0;
            font-size: 2rem;
            line-height: 1.2;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .public-title-sub {
            margin: 8px 0 0;
            color: #4b5f7f;
            font-size: 1rem;
            line-height: 1.6;
            max-width: 880px;
        }

        .public-title-action {
            min-width: 100px;
        }

        .card-clean {
            border: 1px solid rgba(0,0,0,0.05);
            border-radius: 18px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
            background: #fff;
        }

        .card-clean .card-body {
            font-size: 0.95rem;
        }

        .form-label {
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.42rem;
        }

        .form-control,
        .form-select {
            border-color: #dbe6f3;
            min-height: 42px;
            font-size: 0.95rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #9fb9ec;
            box-shadow: 0 0 0 0.18rem rgba(52, 104, 203, 0.14);
        }

        .card-clean .card-body > form.row.g-3.align-items-end > [class*="col-"]:last-child {
            margin-left: auto;
        }

        .card-clean .card-body > form.row.g-3.align-items-end > [class*="col-"]:last-child .d-flex {
            width: 100%;
            justify-content: flex-end;
        }

        .table-responsive {
            border-radius: 0;
        }

        .table {
            margin-bottom: 0;
            color: #1e293b;
            font-size: 0.93rem;
            border-radius: 0;
        }

        .table > :not(caption) > * > * {
            border-color: #e5edf7;
            padding: 1rem 0.85rem;
            vertical-align: middle;
        }

        .table thead th {
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.02em;
            text-transform: none;
            color: #1f2937;
            background: #e5e7eb;
            border-bottom: 1px solid #cbd5e1;
            white-space: nowrap;
            border-radius: 0 !important;
        }

        .table tbody td {
            font-size: 0.93rem;
            font-weight: 500;
            color: #1f2937;
        }

        .table-hover tbody tr:hover {
            background: #f7faff;
        }

        .btn {
            font-weight: 500;
        }

        .btn-primary {
            --bs-btn-bg: var(--primary);
            --bs-btn-border-color: var(--primary);
            --bs-btn-hover-bg: var(--primary-dark);
            --bs-btn-hover-border-color: var(--primary-dark);
            --bs-btn-active-bg: var(--primary-dark);
            --bs-btn-active-border-color: var(--primary-dark);
            --bs-btn-disabled-bg: var(--primary);
            --bs-btn-disabled-border-color: var(--primary);
        }

        .action-buttons {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .action-icon-btn {
            width: 36px;
            height: 36px;
            padding: 0;
            border: 1px solid transparent;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            font-size: 1rem;
            font-weight: 700;
            transition: all .2s ease;
            background: #f8fafc;
            box-shadow: 0 1px 0 rgba(15, 23, 42, 0.03);
        }

        .action-icon-btn::after {
            display: none;
        }

        .action-icon-btn:hover,
        .action-icon-btn:focus-visible {
            transform: translateY(-1px);
            outline: none;
        }

        .action-view {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary-dark);
        }

        .action-view:hover,
        .action-view:focus-visible {
            color: #fff;
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .action-doc {
            color: #fff;
            background: #0f766e;
            border-color: #0f5f59;
        }

        .action-doc:hover,
        .action-doc:focus-visible {
            color: #fff;
            background: #0d9488;
            border-color: #0f766e;
        }

        .action-butir {
            color: #fff;
            background: #0891b2;
            border-color: #0e7490;
        }

        .action-butir:hover,
        .action-butir:focus-visible {
            color: #fff;
            background: #0e7490;
            border-color: #155e75;
        }

        .action-history {
            color: #fff;
            background: #4f46e5;
            border-color: #4338ca;
        }

        .action-history:hover,
        .action-history:focus-visible {
            color: #fff;
            background: #4338ca;
            border-color: #3730a3;
        }

        .action-edit {
            color: #fff;
            background: #f59e0b;
            border-color: #d97706;
        }

        .action-edit:hover,
        .action-edit:focus-visible {
            color: #fff;
            background: #d97706;
            border-color: #b45309;
        }

        .action-delete {
            color: #fff;
            background: #dc2626;
            border-color: #b91c1c;
        }

        .action-delete:hover,
        .action-delete:focus-visible {
            color: #fff;
            background: #b91c1c;
            border-color: #991b1b;
        }

        .action-print {
            color: #fff;
            background: #475569;
            border-color: #334155;
        }

        .action-print:hover,
        .action-print:focus-visible {
            color: #fff;
            background: #334155;
            border-color: #1e293b;
        }

        .sipadukar-pagination {
            align-items: center;
        }

        .sipadukar-pagination .page-item {
            margin-left: -1px;
        }

        .sipadukar-pagination .page-item:first-child {
            margin-left: 0;
        }

        .sipadukar-pagination .page-link {
            border-radius: 0;
            border: 1px solid #d7dee9;
            color: var(--primary);
            background: #fff;
            font-weight: 500;
            font-size: 0.92rem;
            min-width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.85rem;
            line-height: 1;
        }

        .sipadukar-pagination .page-link:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: var(--primary-dark);
        }

        .sipadukar-pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: none;
        }

        .sipadukar-pagination .page-item.disabled .page-link {
            color: #94a3b8;
            background: #fff;
            border-color: #d7dee9;
        }

        .sipadukar-pagination .page-item:first-child .page-link,
        .sipadukar-pagination .page-item:last-child .page-link {
            color: #64748b;
            background: #fff;
            border-color: #d7dee9;
        }

        .sipadukar-pagination .page-item:first-child .page-link:hover,
        .sipadukar-pagination .page-item:last-child .page-link:hover {
            color: #475569;
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .sipadukar-pagination .page-item:first-child .page-link {
            border-top-left-radius: 0.45rem;
            border-bottom-left-radius: 0.45rem;
        }

        .sipadukar-pagination .page-item:last-child .page-link {
            border-top-right-radius: 0.45rem;
            border-bottom-right-radius: 0.45rem;
        }

        .app-footer {
            text-align: center;
            font-size: 0.74rem;
            color: rgba(255, 255, 255, 0.92);
            padding: 6px 12px 7px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-top: 1px solid rgba(255, 255, 255, 0.18);
            flex-shrink: 0;
        }

        .app-footer.public-shell {
            max-width: 1280px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            border-top-left-radius: 14px;
            border-top-right-radius: 14px;
            box-shadow: 0 -6px 16px rgba(15, 23, 42, 0.08);
        }

        .app-footer a {
            color: #ffffff;
            font-weight: 700;
        }

        .app-footer-content {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            white-space: nowrap;
        }

        .heart-red {
            color: #ef4444;
        }

        @keyframes shellDrop {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pageFade {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        @media (max-width: 768px) {
            .topbar-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .brand-wrap {
                width: 100%;
                max-width: 100%;
            }

            .profile-meta {
                overflow: hidden;
            }

            .page-wrap {
                padding-top: 208px;
            }

            .content-wrapper {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .public-content-wrapper {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .app-footer-content {
                white-space: normal;
            }

            .action-icon-btn {
                width: 34px;
                height: 34px;
            }

            .action-icon-btn::after {
                display: none;
            }

            .top-shell.public-shell,
            .app-footer.public-shell {
                width: calc(100% - 1rem);
            }

            .top-shell.public-shell.fixed-top {
                top: 8px;
            }

            .top-shell.public-shell .topbar-main {
                border-top-left-radius: 14px;
                border-top-right-radius: 14px;
            }

            .top-shell.public-shell .menu-bar {
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
            }

            .public-shell-container {
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .public-title-main {
                font-size: 1.72rem;
            }
        }

        @media (max-width: 991.98px) {
            .public-content-wrapper {
                max-width: 100%;
                padding-left: 0;
                padding-right: 0;
            }

            .public-shell-container {
                max-width: 100%;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }
        }

        @media (max-width: 767.98px) {
            .card-clean .card-body > form.row.g-3.align-items-end > [class*="col-"]:last-child .d-flex {
                justify-content: flex-start;
            }
        }

        @media (max-width: 575.98px) {
            .public-content-wrapper {
                padding-left: 0;
                padding-right: 0;
            }

            .public-shell-container {
                padding-left: 0.6rem;
                padding-right: 0.6rem;
            }

            .top-shell.public-shell,
            .app-footer.public-shell {
                width: calc(100% - 0.6rem);
            }

            .top-shell.public-shell.fixed-top {
                top: 6px;
            }

            .app-footer.public-shell {
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
            }

            .public-title-hero {
                padding: 15px 14px 13px;
            }

            .public-title-main {
                font-size: 1.5rem;
            }

            .public-title-sub {
                font-size: 0.94rem;
            }

            .brand-title {
                font-size: 1.14rem;
            }

            .brand-subtitle {
                font-size: 0.78rem;
            }
        }
    </style>
</head>
<body>
<?php
    $uri = service('uri');
    $seg1 = (string) $uri->getSegment(1);
    $profileSettingUrl = '/profil';
?>

<div class="top-shell fixed-top <?= $publicMode ? 'public-shell' : ''; ?>">
    <header class="topbar-main">
        <div class="<?= $publicMode ? 'public-shell-container' : 'container-fluid px-4'; ?> topbar-inner">
            <a href="<?= base_url('/'); ?>" class="brand-wrap">
                <div class="brand-logo">
                    <?php $appLogo = app_logo_header_url(); ?>
                    <?php if ($appLogo !== ''): ?>
                        <img src="<?= esc($appLogo); ?>" alt="Logo SIPENA" style="width:44px;height:44px;object-fit:contain;">
                    <?php else: ?>
                        <i class="bi bi-journal-richtext"></i>
                    <?php endif; ?>
                </div>
                <div class="brand-text">
                    <span class="brand-title"><?= esc($brandTitle); ?></span>
                    <span class="brand-subtitle">Sistem Informasi Penjaminan Mutu Internal</span>
                </div>
            </a>

            <?php if (! $publicMode): ?>
                <div class="dropdown profile-dropdown-shell">
                    <button class="profile-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="profile-avatar">
                            <?= esc(strtoupper(substr(session('nama') ?? 'U', 0, 1))); ?>
                        </span>
                        <span class="profile-meta">
                            <span class="profile-name"><?= esc(session('nama') ?? 'Pengguna'); ?></span>
                            <span class="profile-role"><?= esc($jabatanLabel); ?></span>
                        </span>
                        <span class="profile-caret"><i class="bi bi-chevron-down"></i></span>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end profile-dropdown">
                        <li>
                            <a class="dropdown-item" href="<?= base_url($profileSettingUrl); ?>">
                                <i class="bi bi-person-gear"></i>
                                Pengaturan Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="<?= base_url('/logout'); ?>" method="post" class="m-0">
                                <?= csrf_field(); ?>
                                <button type="submit" class="dropdown-item logout-item">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </header>

<nav class="navbar navbar-expand-lg menu-bar">
    <div class="<?= $publicMode ? 'public-shell-container' : 'container-fluid px-4'; ?>">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSipena" aria-controls="navbarSipena" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSipena">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php if ($publicMode): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link <?= $publicActiveMenu === 'dashboard' ? 'active' : ''; ?>" href="<?= base_url('/'); ?>">
                        <i class="bi bi-grid-1x2-fill menu-icon"></i>Beranda
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= $publicActiveMenu === 'peraturan' ? 'active' : ''; ?>" href="<?= base_url('/publik/peraturan'); ?>">
                        <i class="bi bi-book-fill menu-icon"></i>Peraturan
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link menu-link dropdown-toggle <?= in_array($publicActiveMenu, ['kebijakan-mutu', 'kebijakan-spmi', 'audit-mutu-internal'], true) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shield-check menu-icon"></i>Kebijakan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/publik/kebijakan-mutu'); ?>">Kebijakan Mutu</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/publik/kebijakan-spmi'); ?>">Kebijakan SPMI</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/publik/audit-mutu-internal'); ?>">Audit Mutu Internal</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link menu-link dropdown-toggle <?= $publicActiveMenu === 'pedoman-ppepp' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-journals menu-icon"></i>Pedoman PPEPP
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/publik/pedoman-ppepp?jenis_dokumen=Dokumen'); ?>">Dokumen PPEPP</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/publik/pedoman-ppepp?jenis_dokumen=SOP'); ?>">SOP</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/publik/pedoman-ppepp?jenis_dokumen=Formulir'); ?>">Formulir</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= $publicActiveMenu === 'standar-mutu' ? 'active' : ''; ?>" href="<?= base_url('/publik/standar-mutu'); ?>">
                        <i class="bi bi-award-fill menu-icon"></i>Standar Mutu
                    </a>
                </li>

                <li class="nav-item ms-lg-auto mt-2 mt-lg-0">
                    <?php if ($isLoggedIn): ?>
                        <a href="<?= base_url('/dashboard'); ?>" class="nav-link menu-link">
                            <i class="bi bi-arrow-left-circle-fill menu-icon"></i>Kembali ke Dashboard
                        </a>
                    <?php else: ?>
                        <a href="<?= base_url('/login'); ?>" class="nav-link menu-link">
                            <i class="bi bi-box-arrow-in-right menu-icon"></i>Masuk
                        </a>
                    <?php endif; ?>
                </li>
                <?php else: ?>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= $seg1 === 'dashboard' ? 'active' : ''; ?>" href="<?= base_url('/dashboard'); ?>">
                        <i class="bi bi-grid-1x2-fill menu-icon"></i>Dashboard
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link menu-link dropdown-toggle <?= $seg1 === 'peraturan' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-book-fill menu-icon"></i>Peraturan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/peraturan?kategori=Landasan%20Hukum'); ?>">Landasan Hukum</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/peraturan?kategori=Peraturan%20Dikti'); ?>">Peraturan Dikti</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/peraturan?kategori=Peraturan%20Rektor'); ?>">Peraturan Rektor</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link menu-link dropdown-toggle <?= in_array($seg1, ['kebijakan-mutu', 'kebijakan-spmi', 'audit-mutu-internal'], true) ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shield-check menu-icon"></i>Kebijakan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/kebijakan-mutu'); ?>">Kebijakan Mutu</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/kebijakan-spmi'); ?>">Kebijakan SPMI</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/audit-mutu-internal'); ?>">Audit Mutu Internal</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link menu-link dropdown-toggle <?= $seg1 === 'pedoman-ppepp' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-journals menu-icon"></i>Pedoman PPEPP
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/pedoman-ppepp/dokumen'); ?>">Dokumen PPEPP</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/pedoman-ppepp/sop'); ?>">SOP</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/pedoman-ppepp/formulir'); ?>">Formulir</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link menu-link <?= in_array($seg1, ['standar-mutu', 'dokumen-standar'], true) ? 'active' : ''; ?>" href="<?= base_url('/standar-mutu'); ?>">
                        <i class="bi bi-award-fill menu-icon"></i>Standar Mutu
                    </a>
                </li>

                <?php if ($isAdminRole): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link menu-link dropdown-toggle <?= $seg1 === 'master-data' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-database-fill menu-icon"></i>Master Data
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/master-data/jenis-standar'); ?>">Jenis Standar</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/master-data/kategori-standar'); ?>">Kategori Standar</a></li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link menu-link dropdown-toggle <?= $seg1 === 'pengaturan' ? 'active' : ''; ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill menu-icon"></i>Pengaturan
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= base_url('/pengaturan/profil-institusi'); ?>">Profil Institusi</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/pengaturan/pengguna'); ?>">Pengguna</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('/pengaturan/aplikasi'); ?>">Aplikasi</a></li>
                    </ul>
                </li>
                <?php endif; ?>
                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
</div>

    <main class="page-wrap">
        <div class="container-fluid content-wrapper <?= $publicMode ? 'public-content-wrapper' : ''; ?>">
            <?= $this->renderSection('content'); ?>
        </div>
    </main>

    <footer class="app-footer <?= $publicMode ? 'public-shell' : ''; ?>">
        <div class="app-footer-content">
            <span><?= esc($footerText); ?> &copy; <?= date('Y'); ?></span>
            <span class="heart-red"><i class="bi bi-heart-fill"></i></span>
        </div>
    </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    (function () {
        var tooltipElements = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipElements.forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    })();
</script>
</body>
</html>
