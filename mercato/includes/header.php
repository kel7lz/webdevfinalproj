<?php
// ============================================================
// includes/header.php — Customer-side header
// ============================================================
require_once __DIR__ . '/functions.php';
session_boot();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$cart_count   = cart_count();
$user         = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($page_title ?? APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
</head>
<body>

<nav class="nav" id="main-nav">
  <div class="nav__inner">
    <a href="<?= APP_URL ?>/customer/index.php" class="nav__logo">
      <?= APP_NAME ?>
    </a>

    <button class="nav__hamburger" id="hamburger" aria-label="Toggle menu">
      <span></span><span></span><span></span>
    </button>

    <ul class="nav__links" id="nav-links">
      <li><a href="<?= APP_URL ?>/customer/index.php#menu"
             class="nav__link <?= $current_page === 'index' ? 'nav__link--active' : '' ?>">Menu</a></li>
      <li><a href="<?= APP_URL ?>/customer/cart.php"
             class="nav__link <?= $current_page === 'cart' ? 'nav__link--active' : '' ?>">
        Cart
        <?php if ($cart_count > 0): ?>
          <span class="nav__badge"><?= $cart_count ?></span>
        <?php endif ?>
      </a></li>
      <?php if (is_logged_in()): ?>
      <li><a href="<?= APP_URL ?>/customer/orders.php"
             class="nav__link <?= $current_page === 'orders' ? 'nav__link--active' : '' ?>">My Orders</a></li>
      <?php endif ?>
    </ul>

    <div class="nav__actions">
      <?php if (is_logged_in()): ?>
        <div class="nav__user-menu" id="user-menu-wrap">
          <button class="nav__user-btn" id="user-menu-toggle">
            <?= h($user['username']) ?> ▾
          </button>
          <div class="nav__dropdown" id="user-dropdown">
            <a href="<?= APP_URL ?>/customer/profile.php">Edit Profile</a>
            <a href="<?= APP_URL ?>/customer/orders.php">Order History</a>
            <?php if (is_admin()): ?>
            <a href="<?= APP_URL ?>/admin/index.php">Admin Panel</a>
            <?php endif ?>
            <a href="<?= APP_URL ?>/customer/logout.php">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="<?= APP_URL ?>/customer/login.php" class="btn btn--outline btn--sm">Login / Sign Up</a>
      <?php endif ?>
    </div>
  </div>
</nav>

<?= render_flash() ?>

<main class="main-content">