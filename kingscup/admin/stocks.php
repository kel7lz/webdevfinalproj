<?php
// ============================================================
// King's Cup Coffee — Admin Stock Management
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Stock Management';

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_stock') {
    if (csrf_verify()) {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        
        $status = 'Out of Stock';
        if ($stock > 10) {
            $status = 'In Stock';
        } elseif ($stock > 0) {
            $status = 'Low Stock';
        }
        
        db_execute('UPDATE products SET stock = ?, status = ? WHERE id = ?', [$stock, $status, $product_id]);
        
        // Return JSON for AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'status' => $status]);
            exit;
        }
        
        flash_set('success', 'Stock updated successfully!');
    }
    header('Location: stocks.php');
    exit;
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$search = sanitize_string($_GET['search'] ?? '');

// Build query
$where = [];
$params = [];

if ($filter === 'in_stock') {
    $where[] = "status = 'In Stock'";
} elseif ($filter === 'low_stock') {
    $where[] = "status = 'Low Stock'";
} elseif ($filter === 'out_of_stock') {
    $where[] = "status = 'Out of Stock'";
}

if ($search) {
    $where[] = "p.name LIKE ?";
    $params[] = "%$search%";
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$products = db_fetch_all(
    "SELECT p.*, c.name AS cat_name 
     FROM products p 
     JOIN categories c ON c.id = p.category_id 
     $where_sql 
     ORDER BY c.sort_order, p.name",
    $params
);

// Stats
$total_products = db_fetch("SELECT COUNT(*) AS count FROM products")['count'];
$in_stock = db_fetch("SELECT COUNT(*) AS count FROM products WHERE status = 'In Stock'")['count'];
$low_stock = db_fetch("SELECT COUNT(*) AS count FROM products WHERE status = 'Low Stock'")['count'];
$out_of_stock = db_fetch("SELECT COUNT(*) AS count FROM products WHERE status = 'Out of Stock'")['count'];

require_once __DIR__ . '/../includes/admin_header.php';
?>

<!-- Stat Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">Total Products</div>
        <div style="font-size: 28px; font-weight: 700; color: #2A1A0A;"><?= $total_products ?></div>
    </div>
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">In Stock</div>
        <div style="font-size: 28px; font-weight: 700; color: #17864B;"><?= $in_stock ?></div>
    </div>
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">Low Stock</div>
        <div style="font-size: 28px; font-weight: 700; color: #D97706;"><?= $low_stock ?></div>
    </div>
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">Out of Stock</div>
        <div style="font-size: 28px; font-weight: 700; color: #E74C3C;"><?= $out_of_stock ?></div>
    </div>
</div>

<!-- Filters -->
<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
    <a href="stocks.php" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'all' ? 'background: #C8A96E; color: #3B1F0F;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        All
    </a>
    <a href="stocks.php?filter=in_stock" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'in_stock' ? 'background: #17864B; color: #fff;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        In Stock
    </a>
    <a href="stocks.php?filter=low_stock" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'low_stock' ? 'background: #D97706; color: #fff;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        Low Stock
    </a>
    <a href="stocks.php?filter=out_of_stock" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'out_of_stock' ? 'background: #E74C3C; color: #fff;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        Out of Stock
    </a>
</div>

<!-- Products Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php if (empty($products)): ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px; color: #A89282;">
            No products found.
        </div>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
        <div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(60,30,10,.08); overflow: hidden;" id="product-<?= $p['id'] ?>">
            <div style="padding: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 12px;">
                    <div>
                        <h3 style="font-family: 'Playfair Display', serif; font-size: 1.1rem; color: #2A1A0A; margin-bottom: 4px;">
                            <?= h($p['name']) ?>
                        </h3>
                        <p style="font-size: 13px; color: #A89282;"><?= h($p['cat_name']) ?></p>
                    </div>
                    <span style="font-weight: 700; color: #2A1A0A;">₱<?= number_format($p['price'], 2) ?></span>
                </div>
                
                <div style="margin-bottom: 16px;">
                    <span style="display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;
                        <?php
                        if ($p['status'] === 'In Stock') echo 'background: #D4F5E3; color: #17864B;';
                        elseif ($p['status'] === 'Low Stock') echo 'background: #FEF9C3; color: #854D0E;';
                        else echo 'background: #FEE2E2; color: #991B1B;';
                        ?>">
                        <?= h($p['status']) ?>
                    </span>
                </div>

                <form method="POST" onsubmit="updateStock(event, <?= $p['id'] ?>)">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="update_stock">
                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                    
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <label style="font-size: 13px; font-weight: 500; color: #6B5744;">Stock:</label>
                        <input type="number" name="stock" value="<?= $p['stock'] ?>" min="0" 
                               style="width: 80px; padding: 6px 10px; border: 1px solid #E2D9CF; border-radius: 6px; 
                                      font-size: 14px; font-family: 'DM Sans', sans-serif; text-align: center;"
                               onchange="this.form.submit()">
                        <button type="submit" style="background: #3B1F0F; color: #fff; border: none; padding: 6px 14px; 
                                border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; 
                                font-family: 'DM Sans', sans-serif;">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function updateStock(event, productId) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    
    fetch('stocks.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show brief flash
            const product = document.getElementById('product-' + productId);
            const badge = product.querySelector('span[style*="border-radius: 12px"]');
            badge.textContent = data.status;
            
            if (data.status === 'In Stock') {
                badge.style.background = '#D4F5E3';
                badge.style.color = '#17864B';
            } else if (data.status === 'Low Stock') {
                badge.style.background = '#FEF9C3';
                badge.style.color = '#854D0E';
            } else {
                badge.style.background = '#FEE2E2';
                badge.style.color = '#991B1B';
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>