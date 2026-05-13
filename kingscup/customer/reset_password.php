<?php
// ============================================================
// King's Cup Coffee — Reset Password Page
// ============================================================
require_once __DIR__ . '/../includes/auth.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

// Validate token exists
$user = db_fetch(
    'SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW()',
    [$token]
);

if (!$user) {
    $error = 'Invalid or expired reset token. Please request a new one.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $error = auth_handle_reset_password();
    if (!$error) {
        $success = 'Your password has been reset successfully! You can now login.';
    }
}

$page_title = 'Reset Password — ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    <style>
        .auth-page { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--brown-dark); padding: 24px; }
        .auth-card { background: var(--cream); border-radius: 16px; padding: 40px 36px; width: 100%; max-width: 420px; box-shadow: 0 8px 40px rgba(0,0,0,.35); }
        .auth-logo { text-align: center; margin-bottom: 28px; }
        .auth-logo-icon { width: 56px; height: 56px; background: var(--gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-family: 'Playfair Display', serif; font-size: 24px; color: var(--brown-dark); font-weight: 700; margin: 0 auto 12px; }
        .auth-card h1 { font-family: 'Playfair Display', serif; font-size: 22px; color: var(--brown-dark); text-align: center; }
        .auth-card .subtitle { text-align: center; font-size: 13px; color: var(--text-light); margin: 8px 0 24px; }
        .auth-link { text-align: center; font-size: 0.9rem; color: var(--text-mid); margin-top: 20px; }
        .auth-link a { color: var(--gold); font-weight: 600; }
        .auth-link a:hover { color: var(--brown-dark); text-decoration: underline; }
    </style>
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">K</div>
            <h1>Reset Password</h1>
            <p class="subtitle">Enter your new password</p>
        </div>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="flash flash-success"><?= $success ?></div>
            <p class="auth-link">
                <a href="<?= APP_URL ?>/customer/login.php">Go to Login →</a>
            </p>
        <?php elseif ($user): ?>
            <form method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="token" value="<?= h($token) ?>">
                
                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="form-control" 
                               required minlength="8" autocomplete="new-password">
                        <button type="button" class="password-toggle">👁️</button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-control" required minlength="8" autocomplete="new-password">
                        <button type="button" class="password-toggle">👁️</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top: 16px;">
                    Reset Password
                </button>
            </form>
        <?php endif; ?>

        <p class="auth-link">
            <a href="<?= APP_URL ?>/customer/login.php">← Back to Login</a>
        </p>
    </div>
</div>

<script src="<?= ASSETS_URL ?>/js/main.js"></script>
</body>
</html>