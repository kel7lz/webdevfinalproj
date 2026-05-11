<?php
// ============================================================
// customer/product.php — Product Detail Page
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$product = db_fetch(
    'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug 
     FROM products p 
     JOIN categories c ON c.id = p.category_id 
     WHERE p.id = ? AND p.is_available = 1',
    [$product_id]
);

if (!$product) {
    flash_set('error', 'Product not found.');
    redirect(APP_URL . '/customer/menu.php');
}

$page_title = h($product['name']) . ' — ' . APP_NAME;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="product-detail">
    <a href="<?= APP_URL ?>/customer/menu.php?cat=<?= h($product['cat_slug']) ?>" class="product-detail__back">
        ← Back to <?= h($product['cat_name']) ?>
    </a>

    <div class="product-detail__card">
        <div class="product-detail__image">
            <?php if ($product['image_url']): ?>
                <img src="<?= ASSETS_URL ?>/<?= h($product['image_url']) ?>" alt="<?= h($product['name']) ?>">
            <?php else: ?>
                <div style="font-size: 80px;">☕</div>
            <?php endif; ?>
        </div>

        <div class="product-detail__info">
            <h1 class="product-detail__name"><?= h($product['name']) ?></h1>
            <div class="product-detail__meta">
                <?= h($product['cat_name']) ?> 
                <?php if ($product['calories']): ?>
                    • <?= $product['calories'] ?> calories
                <?php endif; ?>
            </div>
            
            <?php if ($product['description']): ?>
                <p class="product-detail__desc"><?= nl2br(h($product['description'])) ?></p>
            <?php endif; ?>

            <div class="product-detail__price"><?= format_money($product['price']) ?></div>

            <form action="<?= APP_URL ?>/customer/cart_action.php" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <div class="qty-row">
                    <span class="qty-label">Quantity</span>
                    <div class="qty-control">
                        <button type="button" class="qty-control__btn" id="qty-minus" aria-label="Decrease quantity">−</button>
                        <span class="qty-control__val" id="qty-value">1</span>
                        <button type="button" class="qty-control__btn" id="qty-plus" aria-label="Increase quantity">+</button>
                    </div>
                    <input type="hidden" name="quantity" id="qty-input" value="1">
                </div>

                <button type="submit" class="btn btn--primary btn--full btn--lg">
                    Add to Cart — <?= format_money($product['price']) ?>
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>