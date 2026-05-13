<?php
// ============================================================
// King's Cup Coffee — Menu Page
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

<div style="max-width: 1100px; margin: 0 auto; padding: 48px 24px;">
    <div style="margin-bottom: 32px;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--brown-dark);">Our Menu</h1>
        <p style="color: var(--text-light);">Brewed to perfection, just for you</p>
    </div>

    <!-- Category Tabs -->
    <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px;">
        <a href="?cat=all" style="padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 600; 
           text-decoration: none; transition: all .2s;
           <?= $active_slug === 'all' ? 'background: var(--brown-dark); color: var(--gold-light);' : 'background: var(--white); color: var(--text-mid); border: 1.5px solid var(--border);' ?>">
            All
        </a>
        <?php foreach ($categories as $cat): ?>
        <a href="?cat=<?= h($cat['slug']) ?>" style="padding: 8px 20px; border-radius: 20px; font-size: 14px; font-weight: 600;
           text-decoration: none; transition: all .2s;
           <?= $active_slug === $cat['slug'] ? 'background: var(--brown-dark); color: var(--gold-light);' : 'background: var(--white); color: var(--text-mid); border: 1.5px solid var(--border);' ?>">
            <?= h($cat['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div style="text-align: center; padding: 80px 0; color: var(--text-light);">
            <p style="font-size: 1.2rem;">No items found in this category.</p>
            <a href="<?= APP_URL ?>/customer/menu.php" class="btn btn-primary" style="margin-top: 16px;">View All Items</a>
        </div>
    <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 24px;">
        <?php foreach ($products as $p): ?>
        <a href="<?= APP_URL ?>/customer/product.php?id=<?= $p['id'] ?>" 
           style="background: var(--white); border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow); 
                  text-decoration: none; color: inherit; transition: transform .2s, box-shadow .2s; display: block;"
           onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 8px 24px rgba(59,31,14,.15)'"
           onmouseout="this.style.transform=''; this.style.boxShadow=''">
            <div style="width: 100%; height: 200px; overflow: hidden; background: var(--cream-dark); display: flex; align-items: center; justify-content: center;">
                <?php if ($p['image_url']): ?>
                    <img src="<?= ASSETS_URL ?>/<?= h($p['image_url']) ?>" alt="<?= h($p['name']) ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <span style="font-size: 4rem;">☕</span>
                <?php endif; ?>
            </div>
            <div style="padding: 16px 20px 20px;">
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--brown-dark); margin-bottom: 6px;">
                    <?= h($p['name']) ?>
                </h3>
                <?php if ($p['calories']): ?>
                    <p style="font-size: 0.8rem; color: var(--text-light); margin-bottom: 8px;"><?= $p['calories'] ?> cal</p>
                <?php endif; ?>
                <p style="font-size: 1.2rem; font-weight: 700; color: var(--brown-dark);">
                    ₱<?= number_format($p['price'], 2) ?>
                </p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>