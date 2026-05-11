<?php
// ============================================================
// customer/profile.php — Edit Profile
// ============================================================
require_once __DIR__ . '/../includes/functions.php';
require_login();

$user = current_user();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        $error = 'Invalid request.';
    } else {
        $username = sanitize_string($_POST['username'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');

        if (empty($username) || empty($email)) {
            $error = 'All fields are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            // Check uniqueness
            $existing = db_fetch(
                'SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?',
                [$email, $username, $user['id']]
            );
            if ($existing) {
                $error = 'Email or username already taken.';
            } else {
                db_execute(
                    'UPDATE users SET username = ?, email = ? WHERE id = ?',
                    [$username, $email, $user['id']]
                );
                $_SESSION['username'] = $username;
                $success = 'Profile updated!';
            }
        }
    }
}

$page_title = 'Edit Profile — ' . APP_NAME;

require_once __DIR__ . '/../includes/header.php';
?>

<div class="profile-page">
    <h1>Edit Profile</h1>

    <?php if ($error): ?>
        <div class="flash flash-error"><?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="flash flash-success"><?= h($success) ?></div>
    <?php endif; ?>

    <form method="POST" style="margin-top: 24px;">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" 
                   value="<?= h($user['username']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" 
                   value="<?= h($user['email']) ?>" required>
        </div>

        <button type="submit" class="btn btn--primary btn--full">Update Profile</button>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>