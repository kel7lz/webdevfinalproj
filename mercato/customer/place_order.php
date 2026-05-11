<?php
// ============================================================
// customer/place_order.php — Create order from cart
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    flash_set('error', 'Invalid request.');
    redirect(APP_URL . '/customer/checkout.php');
}

$cart_details = cart_details();

if (empty($cart_details['items'])) {
    flash_set('error', 'Your cart is empty.');
    redirect(APP_URL . '/customer/menu.php');
}

$order_type = in_array($_POST['order_type'] ?? '', ['dine-in', 'takeout', 'delivery']) 
    ? $_POST['order_type'] 
    : 'dine-in';
$notes = sanitize_string($_POST['notes'] ?? '');
$user = current_user();

try {
    $pdo = get_db();
    $pdo->beginTransaction();

    // Create order
    $order_id = db_insert(
        'INSERT INTO orders (user_id, order_type, status, total_amount, notes) VALUES (?, ?, ?, ?, ?)',
        [$user['id'], $order_type, 'pending', $cart_details['total'], $notes]
    );

    // Create order items
    foreach ($cart_details['items'] as $item) {
        db_insert(
            'INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) VALUES (?, ?, ?, ?, ?)',
            [$order_id, $item['product_id'], $item['quantity'], $item['price'], $item['subtotal']]
        );
    }

    $pdo->commit();

    // Clear cart
    cart_clear();

    // Redirect to payment
    redirect(APP_URL . '/customer/payment.php?order_id=' . $order_id);

} catch (Exception $e) {
    if (isset($pdo)) $pdo->rollBack();
    flash_set('error', 'Failed to place order. Please try again.');
    redirect(APP_URL . '/customer/checkout.php');
}