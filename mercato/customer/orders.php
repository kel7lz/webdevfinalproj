<?php
// ============================================================
// customer/orders.php — Order History
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_login();

$user = current_user();

$orders = db_fetch_all(
    'SELECT * FROM orders WHERE user_id = ? ORDER BY placed_at DESC',
    [$user['id']]
);

$page_title = 'My Orders — ' . APP_NAME;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="orders-page">
    <h1 class="orders-title">My Orders</h1>

    <?php if (empty($orders)): ?>
        <div class="cart-empty">
            <div class="cart-empty__icon">📋</div>
            <h2 class="cart-empty__title">No orders yet</h2>
            <p class="text-muted">Start ordering your favorite drinks!</p>
            <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn--primary mt-6">Browse Menu</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            $items = db_fetch_all(
                'SELECT oi.*, p.name, p.image_url 
                 FROM order_items oi 
                 JOIN products p ON p.id = oi.product_id 
                 WHERE oi.order_id = ?',
                [$order['id']]
            );
        ?>
        <div class="order-card">
            <div class="order-card__header">
                <div class="order-card__header-info">
                    <div class="order-card__id">Order #<?= $order['id'] ?></div>
                    <div class="order-card__date"><?= format_datetime($order['placed_at']) ?></div>
                </div>
                <div><?= order_status_badge($order['status']) ?></div>
            </div>
            
            <div class="order-card__body">
                <div class="order-item-row order-item-row--header">
                    <div>Item</div>
                    <div>Qty</div>
                    <div>Price</div>
                    <div>Subtotal</div>
                </div>
                <?php foreach ($items as $item): ?>
                <div class="order-item-row">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div class="order-item-thumb">
                            <?php if ($item['image_url']): ?>
                                <img src="<?= ASSETS_URL ?>/<?= h($item['image_url']) ?>" alt="">
                            <?php else: ?>☕<?php endif; ?>
                        </div>
                        <?= h($item['name']) ?>
                    </div>
                    <div><?= $item['quantity'] ?></div>
                    <div><?= format_money($item['unit_price']) ?></div>
                    <div><strong><?= format_money($item['subtotal']) ?></strong></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="order-card__footer">
                <span>Total:</span>
                <span class="order-card__total"><?= format_money($order['total_amount']) ?></span>
                
                <?php if ($order['status'] === 'pending'): ?>
                    <a href="<?= APP_URL ?>/customer/payment.php?order_id=<?= $order['id'] ?>" 
                       class="btn btn--secondary btn--sm">Pay Now</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>