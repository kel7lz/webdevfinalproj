<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Products';

// Handle product add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Invalid request.');
    } else {
        $action = $_POST['action'] ?? '';
        $name = sanitize_string($_POST['name'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $price = (float)($_POST['price'] ?? 0);
        $description = sanitize_string($_POST['description'] ?? '');
        $calories = !empty($_POST['calories']) ? (int)$_POST['calories'] : null;
        $image_url = sanitize_string($_POST['image_url'] ?? '');
        $is_available = isset($_POST['is_available']) ? 1 : 0;

        if (empty($name) || empty($category_id) || $price <= 0) {
            flash_set('error', 'Name, category, and price are required.');
        } elseif ($action === 'add') {
            db_insert(
                'INSERT INTO products (category_id, name, description, price, image_url, calories, is_available) VALUES (?,?,?,?,?,?,?)',
                [$category_id, $name, $description, $price, $image_url, $calories, $is_available]
            );
            flash_set('success', 'Product added!');
        } elseif ($action === 'edit') {
            $product_id = (int)($_POST['product_id'] ?? 0);
            db_execute(
                'UPDATE products SET category_id=?, name=?, description=?, price=?, image_url=?, calories=?, is_available=? WHERE id=?',
                [$category_id, $name, $description, $price, $image_url, $calories, $is_available, $product_id]
            );
            flash_set('success', 'Product updated!');
        }
    }
    redirect(APP_URL . '/admin/products.php');
}

// Handle delete
if (isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    db_execute('DELETE FROM products WHERE id = ?', [$product_id]);
    flash_set('success', 'Product deleted.');
    redirect(APP_URL . '/admin/products.php');
}

$products = db_fetch_all('SELECT p.*, c.name AS cat_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY c.sort_order, p.name');
$categories = db_fetch_all('SELECT * FROM categories ORDER BY sort_order');

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div style="margin-bottom:16px;">
    <button class="btn btn--primary" onclick="document.getElementById('add-form').style.display='block';this.style.display='none';">+ Add Product</button>
</div>

<div id="add-form" style="display:none;" class="admin-form-card mb-6">
    <h3>Add Product</h3>
    <form method="POST">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add">
        <div class="form-group"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Category</label><select name="category_id" class="form-control" required><?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= h($c['name']) ?></option><?php endforeach; ?></select></div>
        <div class="form-group"><label class="form-label">Price (₱)</label><input type="number" name="price" class="form-control" step="0.01" required></div>
        <div class="form-group"><label class="form-label">Description</label><textarea name="description" class="form-control"></textarea></div>
        <div class="form-group"><label class="form-label">Calories</label><input type="number" name="calories" class="form-control"></div>
        <div class="form-group"><label class="form-label">Image URL</label><input type="text" name="image_url" class="form-control" placeholder="images/filename.jpg"></div>
        <div class="form-group"><label><input type="checkbox" name="is_available" checked> Available</label></div>
        <button type="submit" class="btn btn--primary">Save Product</button>
    </form>
</div>

<div class="admin-table-wrap">
    <table class="admin-table">
        <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Available</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($products as $p): ?>
            <tr>
                <td><?= h($p['name']) ?></td>
                <td><?= h($p['cat_name']) ?></td>
                <td><?= format_money($p['price']) ?></td>
                <td><?= $p['is_available'] ? '✅' : '❌' ?></td>
                <td>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn--danger btn--sm" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>