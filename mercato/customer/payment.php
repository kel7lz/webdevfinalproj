<?php
// ============================================================
// customer/payment.php — Payment handling
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_login();

$user = current_user();

// Determine if POST (AJAX) or GET (display page)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle AJAX payment actions
    if (!csrf_verify()) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create_intent') {
        $order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (int)($_POST['order_id'] ?? 0);
        $payment_method = $_POST['payment_method'] ?? 'gcash';

        $order = db_fetch(
            'SELECT * FROM orders WHERE id = ? AND user_id = ?',
            [$order_id, $user['id']]
        );

        if (!$order) {
            echo json_encode(['error' => 'Order not found.']);
            exit;
        }

        // Create PayMongo payment intent
        $amount_centavos = (int)($order['total_amount'] * 100);
        $result = paymongo_create_intent($amount_centavos, 'Order #' . $order_id);

        if (!empty($result['error'])) {
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $intent = $result['data'];
        $client_key = $intent['attributes']['client_key'] ?? '';

        // Save payment record
        db_insert(
            'INSERT INTO payments (order_id, paymongo_intent_id, paymongo_client_key, method, status, amount) VALUES (?, ?, ?, ?, ?, ?)',
            [$order_id, $intent['id'], $client_key, $payment_method, 'pending', $order['total_amount']]
        );

        echo json_encode([
            'success' => true,
            'intent_id' => $intent['id'],
            'client_key' => $client_key,
            'redirect_url' => $intent['attributes']['next_action']['redirect']['url'] ?? null,
        ]);
        exit;
    }

    if ($action === 'verify') {
        $intent_id = $_POST['intent_id'] ?? '';
        $client_key = $_POST['client_key'] ?? '';

        $result = paymongo_get_intent($intent_id);

        if (!empty($result['error'])) {
            echo json_encode(['error' => 'Failed to verify payment.']);
            exit;
        }

        $status = $result['data']['attributes']['status'] ?? '';

        if ($status === 'succeeded') {
            // Update payment record
            $payment = db_fetch(
                'SELECT * FROM payments WHERE paymongo_intent_id = ?',
                [$intent_id]
            );

            if ($payment) {
                db_execute(
                    'UPDATE payments SET status = ?, paid_at = NOW() WHERE id = ?',
                    ['paid', $payment['id']]
                );

                // Update order status
                db_execute(
                    'UPDATE orders SET status = ? WHERE id = ?',
                    ['paid', $payment['order_id']]
                );

                echo json_encode(['success' => true]);
            }
        } else {
            echo json_encode(['error' => 'Payment not yet completed. Status: ' . $status]);
        }
        exit;
    }
}

// GET request — show payment page
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$order = db_fetch(
    'SELECT * FROM orders WHERE id = ? AND user_id = ?',
    [$order_id, $user['id']]
);

if (!$order) {
    flash_set('error', 'Order not found.');
    redirect(APP_URL . '/customer/orders.php');
}

$page_title = 'Payment — ' . APP_NAME;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="payment-page">
    <div class="payment-card">
        <h2 class="payment-card__title">Complete Payment</h2>
        <p class="payment-card__subtitle">Order #<?= $order['id'] ?> — <?= format_money($order['total_amount']) ?></p>

        <?= csrf_field() ?>
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

        <div class="payment-methods">
            <div class="payment-method-option selected">
                <input type="radio" name="payment_method" value="gcash" id="pm-gcash" 
                       class="js-payment-method" checked>
                <label for="pm-gcash" class="payment-method-label">
                    <span class="payment-method-label__icon">💙</span>
                    <span class="payment-method-label__name">GCash</span>
                </label>
            </div>
            <div class="payment-method-option">
                <input type="radio" name="payment_method" value="paymaya" id="pm-maya" 
                       class="js-payment-method">
                <label for="pm-maya" class="payment-method-label">
                    <span class="payment-method-label__icon">💜</span>
                    <span class="payment-method-label__name">Maya</span>
                </label>
            </div>
            <div class="payment-method-option">
                <input type="radio" name="payment_method" value="card" id="pm-card" 
                       class="js-payment-method">
                <label for="pm-card" class="payment-method-label">
                    <span class="payment-method-label__icon">💳</span>
                    <span class="payment-method-label__name">Card</span>
                </label>
            </div>
        </div>

        <div id="payment-action-section">
            <button id="js-pay-btn" class="btn btn--primary btn--full btn--lg">
                <span id="js-pay-spinner" class="spinner" style="display:none;"></span>
                Pay <?= format_money($order['total_amount']) ?>
            </button>
        </div>

        <div id="paymongo-element" style="display:none;"></div>
        <div id="js-pay-error" class="flash flash-error" style="display:none; margin-top:12px;"></div>

        <p style="margin-top: 20px; font-size: 13px; color: var(--color-text-muted);">
            <a href="<?= APP_URL ?>/customer/orders.php">← Back to Orders</a>
        </p>
    </div>
</div>

<!-- Load PayMongo JS SDK -->
<script src="https://js.paymongo.com/v1" defer></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>