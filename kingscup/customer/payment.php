<?php
// ============================================================
// King's Cup Coffee - Payment Page
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_login();

$user = current_user();
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Handle AJAX payment actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    if (!csrf_verify()) {
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    $action = $_POST['action'] ?? '';
    
    if ($action === 'create_intent') {
        $order = db_fetch(
            'SELECT * FROM orders WHERE id = ? AND user_id = ?',
            [$order_id, $user['id']]
        );

        if (!$order) {
            echo json_encode(['error' => 'Order not found.']);
            exit;
        }

        $amount_centavos = (int)($order['total'] * 100);
        
        // Create PayMongo payment link
        $result = paymongo_create_payment_link($amount_centavos, 'Order #' . $order_id);

        if (!empty($result['error'])) {
            echo json_encode(['error' => $result['error']]);
            exit;
        }

        $checkout_url = $result['data']['attributes']['checkout_url'] ?? null;

        if ($checkout_url) {
            echo json_encode([
                'success' => true,
                'checkout_url' => $checkout_url
            ]);
        } else {
            echo json_encode(['error' => 'Failed to create payment link']);
        }
        exit;
    }

    if ($action === 'verify') {
        // For demo: mark as paid
        db_execute(
            'UPDATE orders SET payment_status = ?, status = ? WHERE id = ?',
            ['Paid', 'Preparing', $order_id]
        );
        
        db_insert(
            'INSERT INTO payments (order_id, method, status, amount, paid_at) VALUES (?, ?, ?, ?, NOW())',
            [$order_id, $_POST['payment_method'] ?? 'Cash', 'paid', db_fetch('SELECT total FROM orders WHERE id = ?', [$order_id])['total']]
        );

        echo json_encode(['success' => true]);
        exit;
    }
}

// GET request - show payment page
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

<div style="max-width: 500px; margin: 0 auto; padding: 48px 24px;">
    <div style="background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); padding: 32px; text-align: center;">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; color: var(--brown-dark); margin-bottom: 8px;">
            Complete Payment
        </h2>
        <p style="color: var(--text-light); margin-bottom: 24px;">
            Order #<?= $order['id'] ?> — ₱<?= number_format($order['total'], 2) ?>
        </p>

        <?php if ($order['payment_status'] === 'Paid'): ?>
            <div style="background: #D4F5E3; border: 1px solid #C3E6CB; border-radius: 12px; padding: 24px; margin-bottom: 20px;">
                <div style="font-size: 3rem; margin-bottom: 12px;">✅</div>
                <h3 style="color: #17864B; margin-bottom: 8px;">Payment Complete!</h3>
                <p style="color: #155724; font-size: 14px;">Your order is being prepared.</p>
                <a href="<?= APP_URL ?>/customer/orders.php" class="btn btn-primary btn-full" style="margin-top: 16px;">
                    View My Orders
                </a>
            </div>
        <?php else: ?>
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 12px; color: var(--text-dark);">
                    Select Payment Method
                </label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px;">
                    <label style="display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px; border: 2px solid var(--border); border-radius: 12px; cursor: pointer; transition: all .2s;" 
                           onmouseover="this.style.borderColor='var(--gold)'" 
                           onmouseout="this.style.borderColor='var(--border)'">
                        <input type="radio" name="payment_method" value="gcash" checked style="display: none;">
                        <span style="font-size: 2rem;">💙</span>
                        <span style="font-size: 14px; font-weight: 600;">GCash</span>
                    </label>
                    <label style="display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 16px; border: 2px solid var(--border); border-radius: 12px; cursor: pointer; transition: all .2s;"
                           onmouseover="this.style.borderColor='var(--gold)'" 
                           onmouseout="this.style.borderColor='var(--border)'">
                        <input type="radio" name="payment_method" value="card" style="display: none;">
                        <span style="font-size: 2rem;">💳</span>
                        <span style="font-size: 14px; font-weight: 600;">Card</span>
                    </label>
                </div>
            </div>

            <button onclick="processPayment()" class="btn btn-primary btn-full btn-lg" id="payBtn">
                Pay ₱<?= number_format($order['total'], 2) ?>
            </button>
            
            <div id="paymentError" style="display: none; background: #FEE2E2; color: #991B1B; border-radius: 8px; padding: 12px; margin-top: 12px; font-size: 14px;"></div>
        <?php endif; ?>

        <p style="margin-top: 20px;">
            <a href="<?= APP_URL ?>/customer/orders.php" style="color: var(--text-light); font-size: 14px; text-decoration: underline;">
                ← Back to Orders
            </a>
        </p>
    </div>
</div>

<script>
async function processPayment() {
    const btn = document.getElementById('payBtn');
    const errBox = document.getElementById('paymentError');
    
    btn.disabled = true;
    btn.innerHTML = '⏳ Processing...';
    errBox.style.display = 'none';

    try {
        // Try PayMongo first
        const res = await fetch('<?= APP_URL ?>/customer/payment.php?order_id=<?= $order_id ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=create_intent&csrf_token=<?= csrf_token() ?>'
        });
        
        const data = await res.json();
        
        if (data.checkout_url) {
            // Redirect to PayMongo checkout
            window.location.href = data.checkout_url;
        } else {
            // Fallback: mark as paid directly (for demo)
            const verifyRes = await fetch('<?= APP_URL ?>/customer/payment.php?order_id=<?= $order_id ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=verify&payment_method=Cash&csrf_token=<?= csrf_token() ?>'
            });
            
            const verifyData = await verifyRes.json();
            if (verifyData.success) {
                window.location.reload();
            }
        }
    } catch (e) {
        errBox.textContent = 'Payment failed. Please try again.';
        errBox.style.display = 'block';
        btn.disabled = false;
        btn.innerHTML = 'Pay ₱<?= number_format($order['total'], 2) ?>';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>