<?php
// ============================================================
// King's Cup Coffee - Shopping Cart
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

$page_title = 'Cart — ' . APP_NAME;
$cart_details = cart_details();

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 900px; margin: 0 auto; padding: 48px 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2rem; color: var(--brown-dark);">
            My Cart
        </h1>
        <span style="color: var(--text-light);"><?= APP_NAME ?></span>
    </div>

    <?php if (empty($cart_details['items'])): ?>
        <div style="text-align: center; padding: 80px 0;">
            <div style="font-size: 4rem; margin-bottom: 16px;">🛒</div>
            <h2 style="font-family: 'Playfair Display', serif; color: var(--text-light); margin-bottom: 12px;">
                Your cart is empty
            </h2>
            <p style="color: var(--text-light); margin-bottom: 24px;">
                Looks like you haven't added anything yet.
            </p>
            <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn-primary btn-lg">
                Browse Menu
            </a>
        </div>
    <?php else: ?>
        <div style="background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: left; padding: 16px; background: var(--cream-dark); font-size: 12px; text-transform: uppercase; color: var(--text-light);">Product</th>
                        <th style="text-align: center; padding: 16px; background: var(--cream-dark); font-size: 12px; text-transform: uppercase; color: var(--text-light);">Quantity</th>
                        <th style="text-align: right; padding: 16px; background: var(--cream-dark); font-size: 12px; text-transform: uppercase; color: var(--text-light);">Price</th>
                        <th style="text-align: right; padding: 16px; background: var(--cream-dark); font-size: 12px; text-transform: uppercase; color: var(--text-light);">Subtotal</th>
                        <th style="text-align: center; padding: 16px; background: var(--cream-dark);"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_details['items'] as $item): ?>
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding: 16px; display: flex; align-items: center; gap: 12px;">
                            <div style="width: 48px; height: 48px; border-radius: 8px; background: var(--cream-dark); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <?php if ($item['image_url']): ?>
                                    <img src="<?= ASSETS_URL ?>/<?= h($item['image_url']) ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    ☕
                                <?php endif; ?>
                            </div>
                            <span style="font-weight: 500;"><?= h($item['name']) ?></span>
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                                <button onclick="updateCart(<?= $item['product_id'] ?>, 'dec')" 
                                        style="width: 32px; height: 32px; border: 1px solid var(--border); background: var(--cream); border-radius: 6px; cursor: pointer; font-size: 18px;">
                                    −
                                </button>
                                <span style="font-weight: 600; min-width: 20px; text-align: center;"><?= $item['quantity'] ?></span>
                                <button onclick="updateCart(<?= $item['product_id'] ?>, 'inc')" 
                                        style="width: 32px; height: 32px; border: 1px solid var(--border); background: var(--cream); border-radius: 6px; cursor: pointer; font-size: 18px;">
                                    +
                                </button>
                            </div>
                        </td>
                        <td style="padding: 16px; text-align: right;">₱<?= number_format($item['price'], 2) ?></td>
                        <td style="padding: 16px; text-align: right; font-weight: 700;">₱<?= number_format($item['subtotal'], 2) ?></td>
                        <td style="padding: 16px; text-align: center;">
                            <button onclick="updateCart(<?= $item['product_id'] ?>, 'remove')" 
                                    style="background: none; border: none; color: var(--red); cursor: pointer; font-size: 13px; font-weight: 600;">
                                Remove
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); padding: 24px; margin-top: 24px;">
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border);">
                <span style="color: var(--text-light);">Subtotal</span>
                <span style="font-weight: 600;">₱<?= number_format($cart_details['total'], 2) ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 16px 0; font-size: 1.2rem;">
                <span style="font-weight: 700; color: var(--brown-dark);">Total</span>
                <span style="font-weight: 700; color: var(--brown-dark);">₱<?= number_format($cart_details['total'], 2) ?></span>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn-outline" style="flex: 1; text-align: center;">
                    Continue Shopping
                </a>
                <a href="<?= APP_URL ?>/customer/checkout.php" class="btn btn-primary" style="flex: 1; text-align: center;">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
async function updateCart(productId, action) {
    try {
        const res = await fetch('<?= APP_URL ?>/ajax/cart_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=${action}&product_id=${productId}&csrf_token=<?= csrf_token() ?>`
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        }
    } catch (e) {
        console.error('Cart update failed:', e);
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>