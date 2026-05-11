<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_verify()) {
    flash_set('error', 'Invalid request.');
    redirect(APP_URL . '/customer/orders.php');
}

$user = current_user();
$order_id = (int)($_POST['order_id'] ?? 0);
$payment_method = $_POST['payment_method'] ?? 'gcash';

$order = db_fetch('SELECT * FROM orders WHERE id = ? AND user_id = ?', [$order_id, $user['id']]);

if (!$order) {
    flash_set('error', 'Order not found.');
    redirect(APP_URL . '/customer/orders.php');
}

// For demo: mark as paid immediately
// In production, integrate with PayMongo API here
db_execute('UPDATE orders SET status = ? WHERE id = ?', ['paid', $order_id]);

// Create payment record
db_insert(
    'INSERT INTO payments (order_id, method, status, amount, paid_at) VALUES (?, ?, ?, ?, NOW())',
    [$order_id, $payment_method, 'paid', $order['total_amount']]
);

flash_set('success', 'Payment successful! Your order is being prepared.');
redirect(APP_URL . '/customer/orders.php');