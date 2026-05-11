<?php
// ============================================================
// customer/checkout.php — Checkout / Order Review
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_login();

$cart_details = cart_details();

if (empty($cart_details['items'])) {
    flash_set('error', 'Your cart is empty.');
    redirect(APP_URL . '/customer/menu.php');
}

$page_title = 'Checkout — ' . APP_NAME;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="checkout-page">
    <div class="checkout-title">
        <h1>Checkout</h1>
        <span class="checkout-title__brand"><?= APP_NAME ?></span>
    </div>

    <!-- Order Summary -->
    <div class="checkout-section">
        <h3 class="checkout-section__title">Order Summary</h3>
        <table class="order-items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_details['items'] as $item): ?>
                <tr>
                    <td>
                        <div class="order-item-name">
                            <div class="order-item-thumb">
                                <?php if ($item['image_url']): ?>
                                    <img src="<?= ASSETS_URL ?>/<?= h($item['image_url']) ?>" alt="">
                                <?php else: ?>
                                    ☕
                                <?php endif; ?>
                            </div>
                            <?= h($item['name']) ?>
                        </div>
                    </td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= format_money($item['price']) ?></td>
                    <td><?= format_money($item['subtotal']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="checkout-total" style="margin-top: 16px; padding-top: 16px; border-top: 2px solid var(--color-border);">
            <span>Total Amount:</span>
            <strong><?= format_money($cart_details['total']) ?></strong>
        </div>
    </div>

    <!-- Order Form -->
    <form action="<?= APP_URL ?>/customer/place_order.php" method="POST" class="checkout-section">
        <?= csrf_field() ?>
        <h3 class="checkout-section__title">Order Details</h3>
        
        <div class="form-group">
            <label class="form-label">Order Type</label>
            <select name="order_type" class="form-control" required>
                <option value="dine-in">Dine In</option>
                <option value="takeout">Takeout</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Special Instructions (optional)</label>
            <textarea name="notes" class="form-control" rows="3" 
                      placeholder="Any special requests or allergies?"></textarea>
        </div>

        <button type="submit" class="btn btn--primary btn--full btn--lg">
            Place Order — <?= format_money($cart_details['total']) ?>
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>