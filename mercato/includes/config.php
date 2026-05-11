<?php
// ============================================================
// includes/config.php — Shared application configuration
// ============================================================

// ── Database ────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'mercato_cafe');
define('DB_USER', 'root');          // ← change in production
define('DB_PASS', '');              // ← change in production
define('DB_CHARSET', 'utf8mb4');

// ── Application ─────────────────────────────────────────────
define('APP_NAME',  "King's Cup");
define('APP_URL',   'http://localhost/mercato');   // no trailing slash
define('APP_ENV',   'development');                // 'production' in prod

// ── PayMongo (test keys) ─────────────────────────────────────
// Replace with your own keys from https://dashboard.paymongo.com
define('PAYMONGO_SECRET_KEY',     'sk_test_REPLACE_WITH_YOUR_SECRET_KEY');
define('PAYMONGO_PUBLIC_KEY',     'pk_test_REPLACE_WITH_YOUR_PUBLIC_KEY');
define('PAYMONGO_API_BASE',       'https://api.paymongo.com/v1');

// ── Paths ────────────────────────────────────────────────────
define('ROOT_PATH',     dirname(__DIR__));
define('INCLUDES_PATH', __DIR__);
define('ASSETS_URL',    APP_URL . '/assets');

// ── Session ──────────────────────────────────────────────────
define('SESSION_NAME', 'mercato_sess');

// ── Error reporting ──────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ── Currency ─────────────────────────────────────────────────
define('CURRENCY_SYMBOL', '₱');

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}