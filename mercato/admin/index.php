<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Dashboard';
$total_orders = db_fetch('SELECT COUNT(*) AS count FROM orders')['count'];
$total_revenue = db_fetch('SELECT SUM(total_amount) AS total FROM orders WHERE status != "cancelled"')['total'] ?? 0;
$total_products = db_fetch('SELECT COUNT(*) AS count FROM products')['count'];
$total_customers = db_fetch('SELECT COUNT(*) AS count FROM users WHERE role = "customer"')['count'];
$recent_orders = db_fetch_all('SELECT o.*, u.username FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.placed_at DESC LIMIT 5');

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="stat-grid">
    <div class="stat-card"><div class="stat-card__label">Total Orders</div><div class="stat-card__value"><?= $total_orders ?></div></div>
    <div class="stat-card"><div class="stat-card__label">Total Revenue</div><div class="stat-card__value"><?= format_money($total_revenue) ?></div></div>
    <div class="stat-card"><div class="stat-card__label">Products</div><div class="stat-card__value"><?= $total_products ?></div></div>
    <div class="stat-card"><div class="stat-card__label">Customers</div><div class="stat-card__value"><?= $total_customers ?></div></div>
</div>

<div class="dashboard-grid">
    <div class="admin-table-wrap">
        <div class="admin-table-header"><span class="admin-table-header__title">Recent Orders</span><a href="<?= APP_URL ?>/admin/orders.php" class="btn btn--outline btn--sm">View All</a></div>
        <table class="admin-table">
            <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= h($order['username']) ?></td>
                    <td><?= format_money($order['total_amount']) ?></td>
                    <td><?= order_status_badge($order['status']) ?></td>
                    <td><?= format_date($order['placed_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>