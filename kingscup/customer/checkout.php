<?php
// ============================================================
// King's Cup Coffee - Checkout Page
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

<div style="max-width: 700px; margin: 0 auto; padding: 48px 24px;">
    <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--brown-dark); margin-bottom: 32px;">
        Checkout
    </h1>

    <!-- Order Summary -->
    <div style="background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); padding: 24px; margin-bottom: 24px;">
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--brown-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
            Order Summary
        </h3>
        
        <?php foreach ($cart_details['items'] as $item): ?>
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 40px; height: 40px; border-radius: 8px; background: var(--cream-dark); display: flex; align-items: center; justify-content: center;">
                    <?= $item['image_url'] ? "<img src='" . ASSETS_URL . "/" . h($item['image_url']) . "' alt='' style='width:100%;height:100%;object-fit:cover;border-radius:8px;'>" : '☕' ?>
                </div>
                <div>
                    <div style="font-weight: 500;"><?= h($item['name']) ?></div>
                    <div style="font-size: 13px; color: var(--text-light);">×<?= $item['quantity'] ?></div>
                </div>
            </div>
            <span style="font-weight: 600;">₱<?= number_format($item['subtotal'], 2) ?></span>
        </div>
        <?php endforeach; ?>

        <div style="display: flex; justify-content: space-between; padding-top: 16px; margin-top: 8px; border-top: 2px solid var(--border); font-size: 1.2rem;">
            <span style="font-weight: 700;">Total</span>
            <span style="font-weight: 700; color: var(--brown-dark);">₱<?= number_format($cart_details['total'], 2) ?></span>
        </div>
    </div>

    <!-- Order Form -->
    <form action="<?= APP_URL ?>/ajax/place_order.php" method="POST" 
          style="background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); padding: 24px;">
        <?= csrf_field() ?>
        
        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--brown-dark); margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
            Order Details
        </h3>
        
        <div class="form-group">
            <label class="form-label" for="order_type">Order Type</label>
            <select name="order_type" id="order_type" class="form-control" required>
                <option value="dine-in">Dine In</option>
                <option value="takeout">Takeout</option>
                <option value="delivery">Delivery</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label" for="notes">Special Instructions (optional)</label>
            <textarea name="notes" id="notes" class="form-control" rows="3" 
                      placeholder="Any special requests or allergies?"></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top: 8px;">
            Place Order — ₱<?= number_format($cart_details['total'], 2) ?>
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>