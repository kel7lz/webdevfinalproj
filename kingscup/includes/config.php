<?php
// ============================================================
// King's Cup Coffee - Application Configuration
// ============================================================

// ── Database Configuration ──────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'kingscup_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ── Application Settings ────────────────────────────────────
define('APP_NAME',  "King's Cup");
define('APP_URL',   'http://localhost/kingscup');
define('APP_ENV',   'development');

// ── PayMongo API Keys (Test Mode) ────────────────────────────
define('PAYMONGO_SECRET_KEY', 'sk_test_MuYaE7qCWFcL7CBqAESaBKnh');
define('PAYMONGO_PUBLIC_KEY', 'pk_test_your_public_key_here');
define('PAYMONGO_API_BASE',   'https://api.paymongo.com/v1');

// ── File Paths ──────────────────────────────────────────────
define('ROOT_PATH',     dirname(__DIR__));
define('INCLUDES_PATH', __DIR__);
define('ASSETS_URL',    APP_URL . '/assets');

// ── Session ─────────────────────────────────────────────────
define('SESSION_NAME', 'kingscup_sess');

// ── Currency ────────────────────────────────────────────────
define('CURRENCY_SYMBOL', '₱');

// ── Error Reporting ─────────────────────────────────────────
if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ── PDO Database Connection ─────────────────────────────────
function get_db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s;port=3306', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_TIMEOUT            => 5, // 5 second timeout
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                die('
                <div style="background:#FEE2E2; color:#991B1B; padding:20px; margin:20px; border-radius:8px; font-family:sans-serif;">
                    <h2>❌ Database Connection Failed</h2>
                    <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
                    <p><strong>Please check:</strong></p>
                    <ol>
                        <li>Is MySQL running in XAMPP Control Panel?</li>
                        <li>Did you create the database "kingscup_db"?</li>
                        <li>Did you import database.sql into phpMyAdmin?</li>
                    </ol>
                    <p><strong>Quick Fix:</strong></p>
                    <ol>
                        <li>Open XAMPP Control Panel</li>
                        <li>Click <strong>Start</strong> next to MySQL</li>
                        <li>Open http://localhost/phpmyadmin</li>
                        <li>Create database: <strong>kingscup_db</strong></li>
                        <li>Import the <strong>database.sql</strong> file</li>
                    </ol>
                </div>
                ');
            } else {
                die('A database error occurred. Please try again later.');
            }
        }
    }
    return $pdo;
}

// ── MySQLi Connection ───────────────────────────────────────
function get_mysqli(): mysqli {
    static $mysqli = null;
    if ($mysqli === null) {
        // Suppress warnings - we handle errors manually
        $mysqli = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
        
        if (!$mysqli) {
            if (APP_ENV === 'development') {
                die('
                <div style="background:#FEE2E2; color:#991B1B; padding:20px; margin:20px; border-radius:8px; font-family:sans-serif;">
                    <h2>❌ MySQL Connection Failed</h2>
                    <p><strong>Error:</strong> ' . htmlspecialchars(mysqli_connect_error()) . '</p>
                    <p>Make sure MySQL is running in XAMPP Control Panel!</p>
                </div>
                ');
            } else {
                die('Database connection error. Please try again later.');
            }
        }
        
        mysqli_set_charset($mysqli, DB_CHARSET);
    }
    return $mysqli;
}

// Initialize MySQLi connection for backward compatibility
$conn = get_mysqli();

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}