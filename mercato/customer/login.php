<?php
// ============================================================
// customer/login.php — Login
// ============================================================
require_once __DIR__ . '/../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = auth_handle_login();
    if (!$error) {
        flash_set('success', 'Welcome back!');
        redirect(APP_URL . '/customer/index.php');
    }
}

$page_title = 'Login — ' . APP_NAME;
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
            <h1 class="auth-card__title">Welcome Back</h1>
            <p class="auth-card__subtitle">Sign in to <?= APP_NAME ?></p>

            <?php if ($error): ?>
                <div class="flash flash-error"><?= h($error) ?></div>
            <?php endif; ?>
            <?= render_flash() ?>

            <form method="POST">
                <?= csrf_field() ?>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" 
                           required autocomplete="email" autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           required autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn--primary btn--full btn--lg">
                    Sign In
                </button>
            </form>

            <hr class="auth-divider">

            <p class="auth-link">
                Don't have an account? 
                <a href="<?= APP_URL ?>/customer/register.php">Create one here</a>
            </p>
        </div>
    </div>
</body>
</html>