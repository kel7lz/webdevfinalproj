<?php
// ============================================================
// King's Cup Coffee — Admin Orders Management
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Orders';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    if (csrf_verify()) {
        $order_id = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $valid_statuses = ['Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled'];
        
        if (in_array($status, $valid_statuses)) {
            db_execute('UPDATE orders SET status = ? WHERE id = ?', [$status, $order_id]);
            flash_set('success', 'Order #' . $order_id . ' updated to ' . $status);
        }
    }
    header('Location: orders.php');
    exit;
}

// Get filter
$filter = $_GET['filter'] ?? 'all';

$where = '';
if ($filter !== 'all') {
    $valid_filters = ['Pending', 'Preparing', 'Ready', 'Completed', 'Cancelled'];
    if (in_array($filter, $valid_filters)) {
        $where = "WHERE status = ?";
        $orders = db_fetch_all("SELECT * FROM orders $where ORDER BY id DESC", [$filter]);
    } else {
        $orders = db_fetch_all("SELECT * FROM orders ORDER BY id DESC");
    }
} else {
    $orders = db_fetch_all("SELECT * FROM orders ORDER BY id DESC");
}

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #2A1A0A;">Orders</h2>
</div>

<!-- Filters -->
<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
    <a href="orders.php" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'all' ? 'background: #C8A96E; color: #3B1F0F;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        All Orders
    </a>
    <a href="orders.php?filter=Pending" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'Pending' ? 'background: #FEF9C3; color: #854D0E;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        Pending
    </a>
    <a href="orders.php?filter=Preparing" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'Preparing' ? 'background: #DBEAFE; color: #1D4ED8;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        Preparing
    </a>
    <a href="orders.php?filter=Ready" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'Ready' ? 'background: #D4F5E3; color: #17864B;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        Ready
    </a>
    <a href="orders.php?filter=Completed" style="padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600; text-decoration: none;
        <?= $filter === 'Completed' ? 'background: #E5E7EB; color: #374151;' : 'background: #fff; color: #6B5744; border: 1px solid #E2D9CF;' ?>">
        Completed
    </a>
</div>

<!-- Orders Table -->
<div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(60,30,10,.08); overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Order #</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Customer</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Items</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Total</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Payment</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Status</th>
                    <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="7" style="padding: 40px; text-align: center; color: #A89282;">No orders found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): 
                        $status_class = match(strtolower($order['status'] ?? '')) {
                            'ready' => 'background: #D4F5E3; color: #17864B;',
                            'preparing' => 'background: #DBEAFE; color: #1D4ED8;',
                            'completed' => 'background: #E5E7EB; color: #374151;',
                            'cancelled' => 'background: #FEE2E2; color: #991B1B;',
                            default => 'background: #FEF9C3; color: #854D0E;',
                        };
                    ?>
                    <tr style="border-bottom: 1px solid #E2D9CF;">
                        <td style="padding: 12px 16px; font-weight: 600;">#<?= str_pad($order['id'], 3, '0', STR_PAD_LEFT) ?></td>
                        <td style="padding: 12px 16px;"><?= h($order['customer_name'] ?? '—') ?></td>
                        <td style="padding: 12px 16px; font-size: 13px;"><?= h(substr($order['items'] ?? '', 0, 40)) ?>...</td>
                        <td style="padding: 12px 16px; font-weight: 600;">₱<?= number_format($order['total'], 2) ?></td>
                        <td style="padding: 12px 16px; font-size: 13px;"><?= h($order['payment_method'] ?? $order['payment_status'] ?? '—') ?></td>
                        <td style="padding: 12px 16px;">
                            <form method="POST" style="display: inline;">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <select name="status" onchange="this.form.submit()" 
                                        style="padding: 5px 10px; border: 1px solid #E2D9CF; border-radius: 6px; 
                                               font-size: 12px; font-family: 'DM Sans', sans-serif; cursor: pointer;
                                               <?= $status_class ?>">
                                    <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="Preparing" <?= $order['status'] === 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                                    <option value="Ready" <?= $order['status'] === 'Ready' ? 'selected' : '' ?>>Ready</option>
                                    <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="Cancelled" <?= $order['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td style="padding: 12px 16px; font-size: 13px; color: #6B5744;">
                            <?= date('M d, Y H:i', strtotime($order['created_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>