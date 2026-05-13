<?php
// ============================================================
// King's Cup Coffee - Admin Header
// ============================================================
require_once __DIR__ . '/functions.php';
session_boot();
require_admin();

$admin_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — <?= h($page_title ?? 'Dashboard') ?> | King's Cup</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body class="admin-body">

<div class="admin-layout">
    <aside class="admin-sidebar" id="admin-sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-logo">K</div>
            <div class="sidebar-brand-text">
                <strong>King's Cup</strong>
                <span>Admin Panel</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="<?= APP_URL ?>/admin/dashboard.php" class="nav-item <?= $admin_page === 'dashboard' ? 'active' : '' ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="<?= APP_URL ?>/admin/products.php" class="nav-item <?= $admin_page === 'products' ? 'active' : '' ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
                Products
            </a>
            <a href="<?= APP_URL ?>/admin/orders.php" class="nav-item <?= $admin_page === 'orders' ? 'active' : '' ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                Orders
            </a>
            <a href="<?= APP_URL ?>/admin/customers.php" class="nav-item <?= $admin_page === 'customers' ? 'active' : '' ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                Customers
            </a>
            <a href="<?= APP_URL ?>/admin/stocks.php" class="nav-item <?= $admin_page === 'stocks' ? 'active' : '' ?>">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/></svg>
                Stocks
            </a>
        </nav>
        <div class="sidebar-footer">
            <a href="<?= APP_URL ?>/customer/index.php" class="nav-item">← View Site</a>
            <a href="<?= APP_URL ?>/customer/logout.php" class="nav-item">Logout</a>
        </div>
    </aside>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="topbar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">☰</button>
            <h1 class="topbar-title"><?= h($page_title ?? 'Dashboard') ?></h1>
            <div class="topbar-user">👤 <?= h(current_user()['username'] ?? 'Admin') ?></div>
        </header>

        <?= render_flash() ?>

        <div class="admin-content">