<?php
// ============================================================
// King's Cup Coffee - Customer Header
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-inner">
        <a href="<?= APP_URL ?>/customer/index.php" class="nav-logo">
            <span class="logo-icon">
                <svg width="32" height="32" viewBox="0 0 32 32">
                    <circle cx="16" cy="16" r="14" fill="#C8A96E"/>
                    <text x="16" y="22" text-anchor="middle" fill="#3B1F0F" font-family="'Playfair Display', serif" font-size="18" font-weight="700">K</text>
                </svg>
            </span>
            <span class="logo-text">King's Cup</span>
        </a>

        <button class="hamburger" id="hamburger" aria-label="Toggle menu">☰</button>

        <ul class="nav-links" id="nav-links">
            <li><a href="<?= APP_URL ?>/customer/index.php" class="<?= $current_page === 'index' ? 'active' : '' ?>">Home</a></li>
            <li><a href="<?= APP_URL ?>/customer/menu.php" class="<?= $current_page === 'menu' ? 'active' : '' ?>">Menu</a></li>
            <li>
                <a href="<?= APP_URL ?>/customer/cart.php" class="<?= $current_page === 'cart' ? 'active' : '' ?>">
                    Cart <?php if ($cart_count > 0): ?><span style="background:#C8A96E;color:#3B1F0F;padding:2px 8px;border-radius:12px;font-size:12px;"><?= $cart_count ?></span><?php endif; ?>
                </a>
            </li>
            <?php if (is_logged_in()): ?>
                <li><a href="<?= APP_URL ?>/customer/orders.php" class="<?= $current_page === 'orders' ? 'active' : '' ?>">My Orders</a></li>
            <?php endif; ?>
        </ul>

        <div class="nav-actions">
            <?php if (is_logged_in()): ?>
                <div class="user-menu">
                    <button class="user-menu-btn" id="userMenuBtn">
                        👤 <?= h($user['username']) ?> ▾
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="<?= APP_URL ?>/customer/profile.php">Edit Profile</a>
                        <a href="<?= APP_URL ?>/customer/orders.php">Order History</a>
                        <?php if (is_admin()): ?>
                            <a href="<?= APP_URL ?>/admin/dashboard.php">Admin Panel</a>
                        <?php endif; ?>
                        <a href="<?= APP_URL ?>/customer/logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= APP_URL ?>/customer/login.php" class="btn-login">Login / Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<?= render_flash() ?>

<main class="main-content">