<?php
// ============================================================
// customer/menu.php — Menu listing
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

$page_title = 'Menu — ' . APP_NAME;

$categories = db_fetch_all('SELECT * FROM categories ORDER BY sort_order ASC');
$active_slug = sanitize_string($_GET['cat'] ?? 'all');

if ($active_slug === 'all') {
    $products = db_fetch_all(
        'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE p.is_available = 1
         ORDER BY c.sort_order, p.name'
    );
} else {
    $cat = db_fetch('SELECT id FROM categories WHERE slug = ?', [$active_slug]);
    if (!$cat) { $active_slug = 'all'; }
    $products = db_fetch_all(
        'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE p.is_available = 1 AND c.slug = ?
         ORDER BY p.name',
        [$active_slug]
    );
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="menu-page">
  <div class="menu-header">
    <h1 class="menu-header__title">Menu</h1>
    <p class="text-muted">For you</p>
  </div>

  <div class="category-tabs" role="tablist">
    <button class="category-tab js-cat-tab <?= $active_slug === 'all' ? 'is-active' : '' ?>" data-cat="all" role="tab">All Items</button>
    <?php foreach ($categories as $cat): ?>
    <button class="category-tab js-cat-tab <?= $active_slug === $cat['slug'] ? 'is-active' : '' ?>" data-cat="<?= h($cat['slug']) ?>" role="tab"><?= h($cat['name']) ?></button>
    <?php endforeach; ?>
  </div>

  <?php if (empty($products)): ?>
    <div class="cart-empty" style="padding:var(--space-12) 0;">
      <div class="cart-empty__icon">🍵</div>
      <p class="cart-empty__title">No items found in this category.</p>
      <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn--primary">View All</a>
    </div>
  <?php else: ?>
  <div class="product-grid">
    <?php foreach ($products as $p): ?>
    <a href="<?= APP_URL ?>/customer/product.php?id=<?= $p['id'] ?>" class="product-card js-product-card" data-cat="<?= h($p['cat_slug']) ?>">
      <div class="product-card__image">
        <?php if ($p['image_url']): ?>
          <img src="<?= ASSETS_URL ?>/<?= h($p['image_url']) ?>" alt="<?= h($p['name']) ?>" loading="lazy">
        <?php else: ?>
          ☕
        <?php endif; ?>
      </div>
      <div class="product-card__body">
        <div class="product-card__name"><?= h($p['name']) ?></div>
        <?php if ($p['calories']): ?>
          <div class="product-card__cal"><?= $p['calories'] ?> calories</div>
        <?php endif; ?>
        <div class="product-card__price"><?= format_money($p['price']) ?></div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>