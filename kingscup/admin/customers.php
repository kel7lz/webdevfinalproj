<?php
// ============================================================
// King's Cup Coffee — Admin Customers Management
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Customers';

// Get all customers
$customers = db_fetch_all(
    "SELECT u.*, 
            (SELECT COUNT(*) FROM orders WHERE user_id = u.id) AS order_count,
            (SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = u.id AND payment_status = 'Paid') AS total_spent
     FROM users u 
     WHERE u.role = 'customer' 
     ORDER BY u.created_at DESC"
);

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div style="margin-bottom: 24px;">
    <h2 style="font-family: 'Playfair Display', serif; font-size: 22px; color: #2A1A0A;">Customers</h2>
    <p style="color: #6B5744; font-size: 14px; margin-top: 4px;">
        <?= count($customers) ?> total customers
    </p>
</div>

<!-- Customers Table -->
<div style="background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(60,30,10,.08); overflow: hidden;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">ID</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Username</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Email</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Orders</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Total Spent</th>
                <th style="text-align: left; padding: 12px 16px; font-size: 11px; text-transform: uppercase; color: #A89282; background: #F0EDE8;">Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #A89282;">No customers found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($customers as $c): ?>
                <tr style="border-bottom: 1px solid #E2D9CF;">
                    <td style="padding: 12px 16px;">#<?= $c['id'] ?></td>
                    <td style="padding: 12px 16px; font-weight: 500;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div style="width: 32px; height: 32px; background: #C8A96E; border-radius: 50%; 
                                        display: flex; align-items: center; justify-content: center; 
                                        color: #3B1F0F; font-weight: 700; font-size: 14px;">
                                <?= strtoupper(substr($c['username'], 0, 1)) ?>
                            </div>
                            <?= h($c['username']) ?>
                        </div>
                    </td>
                    <td style="padding: 12px 16px; color: #6B5744;"><?= h($c['email']) ?></td>
                    <td style="padding: 12px 16px; font-weight: 600;"><?= $c['order_count'] ?></td>
                    <td style="padding: 12px 16px; font-weight: 600; color: #C8A96E;">₱<?= number_format($c['total_spent'], 2) ?></td>
                    <td style="padding: 12px 16px; color: #6B5744; font-size: 13px;">
                        <?= date('M d, Y', strtotime($c['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>