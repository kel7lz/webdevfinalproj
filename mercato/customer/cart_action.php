<?php
// ============================================================
// customer/cart_action.php — Cart AJAX/POST handler
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(APP_URL . '/customer/cart.php');
}

if (!csrf_verify()) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        // AJAX request
        http_response_code(403);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }
    flash_set('error', 'Invalid request.');
    redirect(APP_URL . '/customer/cart.php');
}

$action     = $_POST['action'] ?? '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity   = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

// Validate product exists
$product = db_fetch('SELECT id FROM products WHERE id = ? AND is_available = 1', [$product_id]);
if (!$product) {
    $msg = 'Product not available.';
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(404);
        echo json_encode(['error' => $msg]);
        exit;
    }
    flash_set('error', $msg);
    redirect(APP_URL . '/customer/menu.php');
}

switch ($action) {
    case 'add':
        cart_add($product_id, $quantity);
        $msg = 'Item added to cart!';
        break;
    case 'inc':
        $cart = cart_get();
        $current = $cart[$product_id] ?? 0;
        cart_update($product_id, $current + 1);
        $msg = 'Quantity updated.';
        break;
    case 'dec':
        $cart = cart_get();
        $current = $cart[$product_id] ?? 0;
        if ($current <= 1) {
            cart_remove($product_id);
            $msg = 'Item removed.';
        } else {
            cart_update($product_id, $current - 1);
            $msg = 'Quantity updated.';
        }
        break;
    case 'remove':
        cart_remove($product_id);
        $msg = 'Item removed from cart.';
        break;
    case 'clear':
        cart_clear();
        $msg = 'Cart cleared.';
        break;
    default:
        $msg = 'Invalid action.';
        break;
}

// AJAX response
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $details = cart_details();
    echo json_encode([
        'success' => true,
        'message' => $msg,
        'cart_count' => cart_count(),
        'cart' => $details
    ]);
    exit;
}

// Regular POST - redirect
flash_set('success', $msg);
$redirect = $_POST['redirect'] ?? APP_URL . '/customer/cart.php';
redirect($redirect);