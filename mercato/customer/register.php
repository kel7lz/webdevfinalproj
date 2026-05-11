<?php
// ============================================================
// customer/register.php — Registration
// ============================================================
require_once __DIR__ . '/../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = auth_handle_register();
    if (!$error) {
        flash_set('success', 'Account created! Welcome to ' . APP_NAME . '!');
        redirect(APP_URL . '/customer/index.php');
    }
}

$page_title = 'Create Account — ' . APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
</head>
<body>
    <div class="auth-page">
        <div class="auth-card">
            <h1 class="auth-card__title">Create Account</h1>
            <p class="auth-card__subtitle">Join <?= APP_NAME ?></p>

            <?php if ($error): ?>
                <div class="flash flash-error"><?= h($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           required minlength="3" maxlength="60" autocomplete="username"
                           value="<?= h($_POST['username'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           required autocomplete="email"
                           value="<?= h($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           required minlength="8" autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-control" required minlength="8" autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn--primary btn--full btn--lg">
                    Create Account
                </button>
            </form>

            <hr class="auth-divider">

            <p class="auth-link">
                Already have an account? 
                <a href="<?= APP_URL ?>/customer/login.php">Sign in</a>
            </p>
        </div>
    </div>
</body>
</html>