<?php
// ============================================================
// King's Cup Coffee - Order History
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_login();

$user = current_user();

$orders = db_fetch_all(
    'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC',
    [$user['id']]
);

$page_title = 'My Orders — ' . APP_NAME;

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 800px; margin: 0 auto; padding: 48px 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--brown-dark); margin-bottom: 32px;">
        My Orders
    </h1>

    <?php if (empty($orders)): ?>
        <div style="text-align: center; padding: 80px 0;">
            <div style="font-size: 4rem; margin-bottom: 16px;">📋</div>
            <h2 style="font-family: 'Playfair Display', serif; color: var(--text-light); margin-bottom: 12px;">No orders yet</h2>
            <p style="color: var(--text-light); margin-bottom: 24px;">Start ordering your favorite drinks!</p>
            <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn-primary btn-lg">Browse Menu</a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): 
            $items = db_fetch_all(
                'SELECT oi.*, p.name, p.image_url 
                 FROM order_items oi 
                 LEFT JOIN products p ON p.id = oi.product_id 
                 WHERE oi.order_id = ?',
                [$order['id']]
            );
            
            $badge_class = match(strtolower($order['status'] ?? '')) {
                'ready'     => 'badge-ready',
                'preparing' => 'badge-preparing',
                'completed' => 'badge-completed',
                'cancelled' => 'badge-cancelled',
                default     => 'badge-pending',
            };
        ?>
        <div style="background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); margin-bottom: 20px; overflow: hidden;">
            <div style="padding: 16px 20px; background: var(--cream-dark); display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <div style="font-size: 12px; color: var(--text-light); text-transform: uppercase; letter-spacing: .5px;">Order #<?= str_pad($order['id'], 3, '0', STR_PAD_LEFT) ?></div>
                    <div style="font-size: 14px; font-weight: 500;"><?= format_datetime($order['created_at']) ?></div>
                </div>
                <span class="badge <?= $badge_class ?>"><?= h($order['status']) ?></span>
            </div>
            
            <div style="padding: 0 20px;">
                <?php foreach ($items as $item): ?>
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border);">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--cream-dark); display: flex; align-items: center; justify-content: center;">
                        <?= $item['image_url'] ? "<img src='" . ASSETS_URL . "/" . h($item['image_url']) . "' alt='' style='width:100%;height:100%;object-fit:cover;border-radius:8px;'>" : '☕' ?>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500;"><?= h($item['product_name']) ?></div>
                        <div style="font-size: 13px; color: var(--text-light);">×<?= $item['quantity'] ?> · ₱<?= number_format($item['unit_price'], 2) ?></div>
                    </div>
                    <span style="font-weight: 600;">₱<?= number_format($item['subtotal'], 2) ?></span>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="padding: 16px 20px; background: var(--cream-dark); display: flex; justify-content: space-between; align-items: center;">
                <span style="font-weight: 600;">Total</span>
                <span style="font-size: 1.2rem; font-weight: 700; color: var(--brown-dark);">₱<?= number_format($order['total'], 2) ?></span>
                
                <?php if ($order['payment_status'] === 'Unpaid'): ?>
                    <a href="<?= APP_URL ?>/customer/payment.php?order_id=<?= $order['id'] ?>" 
                       class="btn btn-gold btn-sm">Pay Now</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>