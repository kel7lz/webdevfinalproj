<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Orders';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (csrf_verify()) {
        $order_id = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $valid_statuses = ['pending', 'paid', 'preparing', 'ready', 'delivered', 'cancelled'];
        if (in_array($status, $valid_statuses)) {
            db_execute('UPDATE orders SET status = ? WHERE id = ?', [$status, $order_id]);
            flash_set('success', 'Order #' . $order_id . ' updated to ' . $status);
        }
    }
}

$orders = db_fetch_all('SELECT o.*, u.username FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.placed_at DESC');

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-table-wrap">
    <div class="admin-table-header"><span class="admin-table-header__title">All Orders</span></div>
    <table class="admin-table">
        <thead><tr><th>Order #</th><th>Customer</th><th>Type</th><th>Amount</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td>#<?= $order['id'] ?></td>
                <td><?= h($order['username']) ?></td>
                <td><?= ucfirst($order['order_type']) ?></td>
                <td><?= format_money($order['total_amount']) ?></td>
                <td><?= order_status_badge($order['status']) ?></td>
                <td><?= format_date($order['placed_at']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="status" class="status-select" onchange="this.form.submit()">
                            <?php foreach (['pending','paid','preparing','ready','delivered','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>