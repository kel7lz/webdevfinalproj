<?php
// ============================================================
// King's Cup Coffee — Menu Page
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
session_boot();

$page_title = 'Menu — ' . APP_NAME;

// Get all categories
$categories = db_fetch_all('SELECT * FROM categories ORDER BY sort_order ASC');

// Get active category from URL
$active_slug = sanitize_string($_GET['cat'] ?? 'all');

// Get products based on category filter
if ($active_slug === 'all') {
    $products = db_fetch_all(
        'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
         FROM products p
         JOIN categories c ON c.id = p.category_id
         WHERE p.is_available = 1
         ORDER BY c.sort_order, p.name'
    );
} else {
    // Check if category exists
    $cat = db_fetch('SELECT id FROM categories WHERE slug = ?', [$active_slug]);
    if ($cat) {
        $products = db_fetch_all(
            'SELECT p.*, c.name AS cat_name, c.slug AS cat_slug
             FROM products p
             JOIN categories c ON c.id = p.category_id
             WHERE p.is_available = 1 AND c.slug = ?
             ORDER BY p.name',
            [$active_slug]
        );
    } else {
        $products = [];
        $active_slug = 'all';
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div style="max-width: 1100px; margin: 0 auto; padding: 48px 24px;">
    
    <!-- Menu Header -->
    <div style="margin-bottom: 32px;">
        <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; color: var(--brown-dark); margin-bottom: 8px;">
            Our Menu
        </h1>
        <p style="color: var(--text-light); font-size: 1.1rem;">
            Brewed to perfection, just for you
        </p>
    </div>

    <!-- Category Filter Tabs -->
    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 36px;">
        <a href="menu.php?cat=all" 
           style="padding: 10px 24px; border-radius: 25px; font-size: 14px; font-weight: 600; 
                  text-decoration: none; transition: all 0.2s ease; display: inline-block;
                  <?= $active_slug === 'all' 
                      ? 'background: var(--brown-dark); color: var(--gold-light);' 
                      : 'background: var(--white); color: var(--text-mid); border: 2px solid var(--border);' ?>">
            ☕ All Items
        </a>
        <?php foreach ($categories as $cat): ?>
        <a href="menu.php?cat=<?= h($cat['slug']) ?>" 
           style="padding: 10px 24px; border-radius: 25px; font-size: 14px; font-weight: 600; 
                  text-decoration: none; transition: all 0.2s ease; display: inline-block;
                  <?= $active_slug === $cat['slug'] 
                      ? 'background: var(--brown-dark); color: var(--gold-light);' 
                      : 'background: var(--white); color: var(--text-mid); border: 2px solid var(--border);' ?>">
            <?= h($cat['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- Products Grid -->
    <?php if (empty($products)): ?>
        <div style="text-align: center; padding: 80px 0;">
            <div style="font-size: 4rem; margin-bottom: 16px;">🍵</div>
            <h2 style="font-family: 'Playfair Display', serif; color: var(--text-light); margin-bottom: 12px;">
                No items found
            </h2>
            <p style="color: var(--text-light); margin-bottom: 24px;">
                No products available in this category yet.
            </p>
            <a href="menu.php?cat=all" style="display: inline-block; background: var(--brown-dark); color: var(--white); 
               padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background 0.2s;">
                View All Items
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
            <?php foreach ($products as $p): ?>
            <a href="product.php?id=<?= $p['id'] ?>" 
               style="background: var(--white); border-radius: 12px; overflow: hidden; 
                      box-shadow: 0 4px 20px rgba(59,31,14,0.1); text-decoration: none; 
                      color: inherit; transition: all 0.3s ease; display: block;"
               onmouseover="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 8px 30px rgba(59,31,14,0.18)'"
               onmouseout="this.style.transform=''; this.style.boxShadow='0 4px 20px rgba(59,31,14,0.1)'">
                
                <!-- Product Image -->
                <div style="width: 100%; height: 200px; overflow: hidden; background: var(--cream-dark); 
                            display: flex; align-items: center; justify-content: center;">
                    <?php if ($p['image_url']): ?>
                        <img src="../assets/<?= h($p['image_url']) ?>" 
                             alt="<?= h($p['name']) ?>" 
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span style="display: none; font-size: 4rem;">☕</span>
                    <?php else: ?>
                        <span style="font-size: 4rem;">☕</span>
                    <?php endif; ?>
                </div>

                <!-- Product Info -->
                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.2rem; color: var(--brown-dark); 
                                   margin: 0; flex: 1;">
                            <?= h($p['name']) ?>
                        </h3>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <span style="font-size: 0.85rem; color: var(--text-light); background: var(--cream); 
                                     padding: 3px 10px; border-radius: 12px;">
                            <?= h($p['cat_name']) ?>
                        </span>
                        <?php if ($p['calories']): ?>
                            <span style="font-size: 0.8rem; color: var(--text-light);">
                                <?= $p['calories'] ?> cal
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($p['description']): ?>
                        <p style="font-size: 0.9rem; color: var(--text-mid); line-height: 1.5; 
                                  margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 2; 
                                  -webkit-box-orient: vertical; overflow: hidden;">
                            <?= h($p['description']) ?>
                        </p>
                    <?php endif; ?>

                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 1.3rem; font-weight: 700; color: var(--brown-dark);">
                            ₱<?= number_format($p['price'], 2) ?>
                        </span>
                        <span style="font-size: 0.85rem; color: var(--gold); font-weight: 600;">
                            View Details →
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>