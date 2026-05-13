<?php
// ============================================================
// King's Cup Coffee - Save Order (Instore)
// ============================================================
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No data received.']);
    exit;
}

$customer_name  = trim($data['customer_name'] ?? '');
$mobile         = trim($data['mobile'] ?? '');
$payment_method = trim($data['payment_method'] ?? '');
$cart           = $data['cart'] ?? [];
$amount         = (float)($data['amount'] ?? 0);

if (!$customer_name || !$payment_method || empty($cart) || $amount <= 0) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

// Build items summary
$items_summary = implode(', ', array_map(function($i) {
    return $i['name'] . ' ×' . $i['qty'];
}, $cart));

mysqli_begin_transaction($conn);

try {
    $stmt = mysqli_prepare($conn,
        "INSERT INTO orders (customer_name, mobile, items, payment_method, total, payment_status, status, created_at)
         VALUES (?, ?, ?, ?, ?, 'Paid', 'Pending', NOW())"
    );
    mysqli_stmt_bind_param($stmt, 'ssssd', $customer_name, $mobile, $items_summary, $payment_method, $amount);
    mysqli_stmt_execute($stmt);
    $order_id = mysqli_insert_id($conn);

    if (!$order_id) throw new Exception('Failed to insert order.');

    // Insert order items
    $item_stmt = mysqli_prepare($conn,
        "INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    foreach ($cart as $item) {
        $product_id   = (int)$item['id'];
        $product_name = $item['name'];
        $price        = (float)$item['price'];
        $qty          = (int)$item['qty'];
        $subtotal     = $price * $qty;

        mysqli_stmt_bind_param($item_stmt, 'iisdid',
            $order_id, $product_id, $product_name, $price, $qty, $subtotal
        );
        mysqli_stmt_execute($item_stmt);
    }

    mysqli_commit($conn);

    echo json_encode(['success' => true, 'order_id' => $order_id]);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}