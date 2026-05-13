<?php
// ============================================================
// King's Cup Coffee - Core Functions
// ============================================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// ── Session ─────────────────────────────────────────────────
function session_boot(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        session_start();
    }
}

// ── Authentication Helpers ──────────────────────────────────
function is_logged_in(): bool {
    return !empty($_SESSION['user_id']);
}

function current_user(): ?array {
    if (!is_logged_in()) return null;
    static $user = null;
    if ($user === null) {
        $user = db_fetch(
            'SELECT id, username, email, role FROM users WHERE id = ?',
            [$_SESSION['user_id']]
        );
    }
    return $user;
}

function is_admin(): bool {
    $u = current_user();
    return $u && $u['role'] === 'admin';
}

function require_login(string $redirect = ''): void {
    if (!is_logged_in()) {
        $back = $redirect ?: APP_URL . '/customer/login.php';
        header('Location: ' . $back);
        exit;
    }
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: ' . APP_URL . '/customer/login.php');
        exit;
    }
}

// ── CSRF Protection ─────────────────────────────────────────
function csrf_token(): string {
    session_boot();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify(): bool {
    return isset($_POST['csrf_token'])
        && hash_equals(csrf_token(), $_POST['csrf_token']);
}

// ── Input Sanitization ──────────────────────────────────────
function h(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sanitize_string(string $s): string {
    return trim(strip_tags($s));
}

function sanitize_email(string $s): string {
    return filter_var(trim($s), FILTER_SANITIZE_EMAIL);
}

// ── Flash Messages ──────────────────────────────────────────
function flash_set(string $type, string $msg): void {
    session_boot();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function flash_get(): ?array {
    session_boot();
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function render_flash(): string {
    $f = flash_get();
    if (!$f) return '';
    $cls = $f['type'] === 'error' ? 'flash-error' : 'flash-success';
    return '<div class="flash ' . $cls . '">' . h($f['msg']) . '</div>';
}

// ── Cart Functions ──────────────────────────────────────────
function cart_get(): array {
    return $_SESSION['cart'] ?? [];
}

function cart_add(int $product_id, int $qty = 1): void {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

function cart_update(int $product_id, int $qty): void {
    if ($qty <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
}

function cart_remove(int $product_id): void {
    unset($_SESSION['cart'][$product_id]);
}

function cart_clear(): void {
    $_SESSION['cart'] = [];
}

function cart_count(): int {
    $cart = cart_get();
    return array_sum($cart);
}

function cart_details(): array {
    $cart = cart_get();
    if (empty($cart)) {
        return ['items' => [], 'total' => 0.0];
    }

    // Get product IDs
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    
    // Fetch products
    $products = db_fetch_all(
        "SELECT id, name, price, image_url FROM products WHERE id IN ($placeholders) AND is_available = 1",
        $ids
    );

    $items = [];
    $total = 0.0;
    
    // Create lookup array
    $productLookup = [];
    foreach ($products as $p) {
        $productLookup[$p['id']] = $p;
    }

    // Build cart items
    foreach ($cart as $productId => $qty) {
        if (isset($productLookup[$productId])) {
            $p = $productLookup[$productId];
            $subtotal = $p['price'] * $qty;
            $total += $subtotal;
            
            $items[] = [
                'product_id' => $p['id'],
                'name'       => $p['name'],
                'price'      => $p['price'],
                'quantity'   => $qty,
                'subtotal'   => $subtotal,
                'image_url'  => $p['image_url'],
            ];
        }
    }
    
    return ['items' => $items, 'total' => $total];
}

// ── Formatting ──────────────────────────────────────────────
function format_money(float $amount): string {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function format_date(string $datetime): string {
    return date('F j, Y', strtotime($datetime));
}

function format_datetime(string $datetime): string {
    return date('F j, Y g:i A', strtotime($datetime));
}

// ── Order Status Badge ──────────────────────────────────────
function order_status_badge(string $status): string {
    $map = [
        'Pending'   => 'badge-pending',
        'Preparing' => 'badge-preparing',
        'Ready'     => 'badge-ready',
        'Completed' => 'badge-completed',
        'Cancelled' => 'badge-cancelled',
    ];
    $cls = $map[$status] ?? 'badge-pending';
    return '<span class="badge ' . $cls . '">' . ucfirst(h($status)) . '</span>';
}

// ── PayMongo API ────────────────────────────────────────────
function paymongo_request(string $method, string $endpoint, array $payload = []): array {
    $url = PAYMONGO_API_BASE . $endpoint;
    $auth = base64_encode(PAYMONGO_SECRET_KEY . ':');

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . $auth,
        ],
        CURLOPT_CUSTOMREQUEST  => strtoupper($method),
    ]);

    if (!empty($payload)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($response, true);
    if ($decoded === null) {
        return ['error' => 'Invalid PayMongo response', 'http_code' => $httpCode];
    }
    $decoded['http_code'] = $httpCode;
    return $decoded;
}

function paymongo_create_payment_link(int $amount_centavos, string $description = ''): array {
    return paymongo_request('POST', '/links', [
        'data' => [
            'attributes' => [
                'amount'      => $amount_centavos,
                'description' => $description ?: 'King\'s Cup Order',
                'remarks'     => 'Order from King\'s Cup Coffee',
            ]
        ]
    ]);
}

// ── Redirect ────────────────────────────────────────────────
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// ── Generate random token ───────────────────────────────────
function generate_token(int $length = 32): string {
    return bin2hex(random_bytes($length));
}