<?php
// ============================================================
// King's Cup Coffee - Cart AJAX Handler
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

if (!csrf_verify()) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

$action     = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

// Handle clear action without needing a product
if ($action === 'clear') {
    cart_clear();
    echo json_encode([
        'success'    => true,
        'message'    => 'Cart cleared',
        'cart_count' => 0,
        'cart'       => ['items' => [], 'total' => 0],
        'total'      => 0
    ]);
    exit;
}

// Validate product exists for other actions
$product = db_fetch('SELECT id, name, price FROM products WHERE id = ? AND is_available = 1', [$product_id]);
if (!$product) {
    echo json_encode(['success' => false, 'error' => 'Product not available']);
    exit;
}

// Handle different cart actions
switch ($action) {
    case 'add':
        $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
        cart_add($product_id, $quantity);
        $message = 'Item added to cart!';
        break;
        
    case 'inc':
        $cart = cart_get();
        $current = $cart[$product_id] ?? 0;
        cart_update($product_id, $current + 1);
        $message = 'Quantity increased';
        break;
        
    case 'dec':
        $cart = cart_get();
        $current = $cart[$product_id] ?? 0;
        if ($current <= 1) {
            cart_remove($product_id);
            $message = 'Item removed from cart';
        } else {
            cart_update($product_id, $current - 1);
            $message = 'Quantity decreased';
        }
        break;
        
    case 'remove':
        cart_remove($product_id);
        $message = 'Item removed from cart';
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
}

// Get updated cart details
$details = cart_details();

echo json_encode([
    'success'    => true,
    'message'    => $message,
    'cart_count' => cart_count(),
    'cart'       => $details,
    'total'      => $details['total']
]);