<?php
// ============================================================
// King's Cup Coffee — Admin Products Management
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Products';

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    if (csrf_verify()) {
        $name = sanitize_string($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $description = sanitize_string($_POST['description'] ?? '');
        $calories = !empty($_POST['calories']) ? (int)$_POST['calories'] : null;
        $stock = (int)($_POST['stock'] ?? 0);
        
        if (empty($name) || $category_id <= 0 || $price <= 0) {
            flash_set('error', 'Name, category, and price are required.');
        } else {
            db_insert(
                'INSERT INTO products (category_id, name, description, price, calories, stock, status, is_available) VALUES (?, ?, ?, ?, ?, ?, ?, 1)',
                [$category_id, $name, $description, $price, $calories, $stock, $stock > 0 ? 'In Stock' : 'Out of Stock']
            );
            flash_set('success', 'Product added successfully!');
        }
    }
    header('Location: products.php');
    exit;
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    db_execute('DELETE FROM products WHERE id = ?', [$product_id]);
    flash_set('success', 'Product deleted.');
    header('Location: products.php');
    exit;
}

// Get all products
$products = db_fetch_all(
    'SELECT p.*, c.name AS cat_name FROM products p 
     JOIN categories c ON c.id = p.category_id 
     ORDER BY c.sort_order, p.name'
);

// Get categories for dropdown
$categories = db_fetch_all('SELECT * FROM categories ORDER BY sort_order');

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #2A1A0A;">Products</h2>
    <button onclick="document.getElementById('addProductForm').style.display='block'; this.style.display='none';" 
            style="background: #C8A96E; color: #3B1F0F; border: none; padding: 10px 20px; border-radius: 8px; 
                   font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif;">
        + Add Product
    </button>
</div>

<!-- Add Product Form -->
<div id="addProductForm" style="display: none; background: #fff; border-radius: 12px; padding: 24px; 
     box-shadow: 0 2px 12px rgba(60,30,10,.08); margin-bottom: 24px;">
    <h3 style="font-family: 'Playfair Display', serif; margin-bottom: 20px; color: #2A1A0A;">Add New Product</h3>
    <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add_product">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div class="form-group">
                <label class="form-label">Product Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Category *</label>
                <select name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= h($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Price (₱) *</label>
                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" min="0" value="0">
            </div>
            <div class="form-group">
                <label class="form-label">Calories (optional)</label>
                <input type="number" name="calories" class="form-control" min="0">
            </div>
            <div class="form-group" style="grid-column: 1 / -1;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 16px;">
            <button type="submit" style="background: #3B1F0F; color: #fff; border: none; padding: 10px 24px; 
                    border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif;">
                Save Product
            </button>
            <button type="button" onclick="document.getElementById('addProductForm').style.display='none'; 
                    document.querySelector('button[onclick*=\"addProductForm\"]').style.display='block';" 
                    style="background: #E2D9CF; color: #2A1A0A; border: none; padding: 10px 24px; 
                    border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'DM Sans', sans-serif;">
                Cancel
            </button>
        </div>
    </form>
</div>

<!-- Products Table -->
<div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(60,30,10,.08); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">ID</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Product</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Category</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Price</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Stock</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #A89282;">No products found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $p): ?>
                <tr style="border-bottom: 1px solid #E2D9CF;">
                    <td style="padding: 12px 16px;">#<?= $p['id'] ?></td>
                    <td style="padding: 12px 16px; font-weight: 500;"><?= h($p['name']) ?></td>
                    <td style="padding: 12px 16px;"><?= h($p['cat_name']) ?></td>
                    <td style="padding: 12px 16px; font-weight: 600;">₱<?= number_format($p['price'], 2) ?></td>
                    <td style="padding: 12px 16px;">
                        <span style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;
                            <?php
                            if ($p['stock'] > 10) echo 'background: #D4F5E3; color: #17864B;';
                            elseif ($p['stock'] > 0) echo 'background: #FEF9C3; color: #854D0E;';
                            else echo 'background: #FEE2E2; color: #991B1B;';
                            ?>">
                            <?= $p['stock'] ?>
                        </span>
                    </td>
                    <td style="padding: 12px 16px;">
                        <a href="?delete=<?= $p['id'] ?>" 
                           onclick="return confirm('Delete this product?')"
                           style="color: #E74C3C; font-size: 13px; font-weight: 600; text-decoration: none;">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>