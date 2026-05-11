<?php
// ============================================================
// customer/cart.php — Shopping Cart
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

$page_title = 'Cart — ' . APP_NAME;
$cart_details = cart_details();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="cart-page">
    <div class="cart-title">
        <h1>My Cart</h1>
        <span class="cart-title__brand"><?= APP_NAME ?></span>
    </div>

    <?php if (empty($cart_details['items'])): ?>
        <div class="cart-empty">
            <div class="cart-empty__icon">🛒</div>
            <h2 class="cart-empty__title">Your cart is empty</h2>
            <p class="text-muted">Looks like you haven't added anything yet.</p>
            <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn--primary mt-6">Browse Menu</a>
        </div>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_details['items'] as $item): ?>
                <tr>
                    <td>
                        <div class="cart-table__product">
                            <div class="cart-table__thumb">
                                <?php if ($item['image_url']): ?>
                                    <img src="<?= ASSETS_URL ?>/<?= h($item['image_url']) ?>" alt="<?= h($item['name']) ?>">
                                <?php else: ?>
                                    ☕
                                <?php endif; ?>
                            </div>
                            <span><?= h($item['name']) ?></span>
                        </div>
                    </td>
                    <td>
                        <div class="cart-qty">
                            <button class="cart-qty__btn js-cart-qty" 
                                    data-product-id="<?= $item['product_id'] ?>" 
                                    data-action="dec">−</button>
                            <span class="cart-qty__val"><?= $item['quantity'] ?></span>
                            <button class="cart-qty__btn js-cart-qty" 
                                    data-product-id="<?= $item['product_id'] ?>" 
                                    data-action="inc">+</button>
                        </div>
                    </td>
                    <td><?= format_money($item['price']) ?></td>
                    <td><strong><?= format_money($item['subtotal']) ?></strong></td>
                    <td>
                        <form method="POST" action="<?= APP_URL ?>/customer/cart_action.php" style="display:inline;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <button type="submit" class="cart-remove">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div class="cart-summary__row">
                <span>Subtotal</span>
                <span><?= format_money($cart_details['total']) ?></span>
            </div>
            <div class="cart-summary__total">
                <span>Total</span>
                <span><?= format_money($cart_details['total']) ?></span>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn--outline" style="flex:1;">
                    Continue Shopping
                </a>
                <a href="<?= APP_URL ?>/customer/checkout.php" class="btn btn--primary" style="flex:1;">
                    Proceed to Checkout
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>