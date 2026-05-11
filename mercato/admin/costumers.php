<?php
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$page_title = 'Customers';
$customers = db_fetch_all('SELECT * FROM users WHERE role = "customer" ORDER BY created_at DESC');

require_once __DIR__ . '/../includes/admin_header.php';
?>

<div class="admin-table-wrap">
    <div class="admin-table-header"><span class="admin-table-header__title">All Customers</span><span style="color:var(--color-text-muted);font-size:14px;"><?= count($customers) ?> total</span></div>
    <table class="admin-table">
        <thead><tr><th>ID</th><th>Username</th><th>Email</th><th>Joined</th></tr></thead>
        <tbody>
            <?php foreach ($customers as $c): ?>
            <tr>
                <td>#<?= $c['id'] ?></td>
                <td><?= h($c['username']) ?></td>
                <td><?= h($c['email']) ?></td>
                <td><?= format_date($c['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../includes/admin_footer.php'; ?>