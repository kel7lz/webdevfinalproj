<?php
// ============================================================
// includes/admin_header.php — Admin panel header
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
  <title>Admin — <?= h($page_title ?? 'Dashboard') ?> | <?= APP_NAME ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/admin.css">
</head>
<body class="admin-body">

<div class="admin-layout">
  <aside class="admin-sidebar" id="admin-sidebar">
    <div class="admin-sidebar__brand">
      <span><?= APP_NAME ?></span>
      <small>Admin Panel</small>
    </div>
    <nav class="admin-sidebar__nav">
      <a href="<?= APP_URL ?>/admin/index.php"
         class="admin-nav__link <?= $admin_page === 'index' ? 'admin-nav__link--active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
        Dashboard
      </a>
      <a href="<?= APP_URL ?>/admin/products.php"
         class="admin-nav__link <?= $admin_page === 'products' ? 'admin-nav__link--active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
        Products
      </a>
      <a href="<?= APP_URL ?>/admin/orders.php"
         class="admin-nav__link <?= $admin_page === 'orders' ? 'admin-nav__link--active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z"/></svg>
        Orders
      </a>
      <a href="<?= APP_URL ?>/admin/customers.php"
         class="admin-nav__link <?= $admin_page === 'customers' ? 'admin-nav__link--active' : '' ?>">
        <svg viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
        Customers
      </a>
    </nav>
    <div class="admin-sidebar__footer">
      <a href="<?= APP_URL ?>/customer/index.php" class="admin-nav__link">← View Site</a>
      <a href="<?= APP_URL ?>/customer/logout.php" class="admin-nav__link">Logout</a>
    </div>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button class="admin-topbar__toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
        <span></span><span></span><span></span>
      </button>
      <h1 class="admin-topbar__title"><?= h($page_title ?? 'Dashboard') ?></h1>
      <div class="admin-topbar__user">
        <?= h(current_user()['username'] ?? 'Admin') ?>
      </div>
    </header>

    <?= render_flash() ?>

    <div class="admin-content">