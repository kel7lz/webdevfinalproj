<?php
// ============================================================
// King's Cup Coffee — Admin Dashboard
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Dashboard';

// Stats
$total_orders = db_fetch("SELECT COUNT(*) AS count FROM orders")['count'] ?? 0;
$total_revenue = db_fetch("SELECT COALESCE(SUM(total), 0) AS total FROM orders WHERE payment_status = 'Paid'")['total'] ?? 0;
$total_products = db_fetch("SELECT COUNT(*) AS count FROM products")['count'] ?? 0;
$pending_orders = db_fetch("SELECT COUNT(*) AS count FROM orders WHERE status = 'Pending'")['count'] ?? 0;

$recent_orders = db_fetch_all("SELECT * FROM orders ORDER BY id DESC LIMIT 10");

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 24px; color: #2A1A0A;">
        Hi, <?= h(current_user()['username'] ?? 'Admin') ?>!
    </h2>
    <p style="color: #6B5744; font-size: 14px; margin-top: 4px;">
        Here's what's happening at your coffee shop today.
    </p>
</div>

<!-- Stat Cards -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">Total Orders</div>
        <div style="font-size: 28px; font-weight: 700; color: #2A1A0A;"><?= $total_orders ?></div>
    </div>
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">Total Revenue</div>
        <div style="font-size: 28px; font-weight: 700; color: #C8A96E;">₱<?= number_format($total_revenue, 0) ?></div>
    </div>
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">Products</div>
        <div style="font-size: 28px; font-weight: 700; color: #2A1A0A;"><?= $total_products ?></div>
    </div>
    <div style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(60,30,10,.08);">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #A89282; margin-bottom: 8px;">Pending Orders</div>
        <div style="font-size: 28px; font-weight: 700; color: #D97706;"><?= $pending_orders ?></div>
    </div>
</div>

<!-- Recent Orders -->
<div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(60,30,10,.08); overflow: hidden;">
    <div style="padding: 18px 20px; border-bottom: 1px solid #E2D9CF;">
        <span style="font-size: 16px; font-weight: 600; color: #2A1A0A;">Recent Orders</span>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Order #</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Customer</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Total</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Status</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recent_orders)): ?>
                <tr>
                    <td colspan="5" style="padding: 40px; text-align: center; color: #A89282;">No orders yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recent_orders as $order): ?>
                <tr style="border-bottom: 1px solid #E2D9CF;">
                    <td style="padding: 12px 16px;">#<?= str_pad($order['id'], 3, '0', STR_PAD_LEFT) ?></td>
                    <td style="padding: 12px 16px;"><?= h($order['customer_name'] ?? '—') ?></td>
                    <td style="padding: 12px 16px;">₱<?= number_format($order['total'], 2) ?></td>
                    <td style="padding: 12px 16px;">
                        <span style="display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;
                            <?php
                            $status = strtolower($order['status'] ?? '');
                            if ($status === 'ready') echo 'background: #D4F5E3; color: #17864B;';
                            elseif ($status === 'preparing') echo 'background: #DBEAFE; color: #1D4ED8;';
                            elseif ($status === 'completed') echo 'background: #E5E7EB; color: #374151;';
                            else echo 'background: #FEF9C3; color: #854D0E;';
                            ?>">
                            <?= h($order['status'] ?? 'Pending') ?>
                        </span>
                    </td>
                    <td style="padding: 12px 16px; font-size: 13px; color: #6B5744;">
                        <?= date('M d, Y', strtotime($order['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>