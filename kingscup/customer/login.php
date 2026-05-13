<?php
// ============================================================
// King's Cup Coffee — Login Page
// ============================================================
require_once __DIR__ . '/../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = auth_handle_login();
    if (!$error) {
        flash_set('success', 'Welcome back!');
        
        // Redirect based on role
        if (is_admin()) {
            redirect(APP_URL . '/admin/dashboard.php');
        } else {
            redirect(APP_URL . '/customer/index.php');
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= ASSETS_URL ?>/css/style.css">
    <style>
        .auth-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--brown-dark);
            padding: 24px;
        }
        .auth-card {
            background: var(--cream);
            border-radius: 16px;
            padding: 40px 36px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 8px 40px rgba(0,0,0,.35);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-logo-icon {
            width: 56px; height: 56px;
            background: var(--gold);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            color: var(--brown-dark);
            font-weight: 700;
            margin: 0 auto 12px;
        }
        .auth-card h1 {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: var(--brown-dark);
            text-align: center;
        }
        .auth-card .subtitle {
            text-align: center;
            font-size: 13px;
            color: var(--text-light);
            margin: 8px 0 24px;
        }
        .auth-link {
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-mid);
            margin-top: 20px;
        }
        .auth-link a { color: var(--gold); font-weight: 600; }
        .auth-link a:hover { color: var(--brown-dark); text-decoration: underline; }
    </style>
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="auth-logo-icon">K</div>
            <h1>Welcome Back</h1>
            <p class="subtitle">Sign in to <?= APP_NAME ?></p>
        </div>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= h($error) ?></div>
        <?php endif; ?>
        <?= render_flash() ?>

        <form method="POST">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label class="form-label" for="email">Username or Email</label>
                <input type="text" id="email" name="email" class="form-control" 
                       required autocomplete="username" autofocus
                       value="<?= h($_POST['email'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" class="form-control" 
                           required autocomplete="current-password">
                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">👁️</button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top: 16px;">
                Sign In
            </button>
        </form>

        <p class="auth-link">
            <a href="<?= APP_URL ?>/customer/forgot_password.php">Forgot your password?</a>
        </p>

        <hr style="border: 1px solid #E2D9CF; margin: 20px 0;">

        <p class="auth-link">
            Don't have an account? 
            <a href="<?= APP_URL ?>/customer/register.php">Create one here</a>
        </p>
    </div>
</div>

<script src="<?= ASSETS_URL ?>/js/main.js"></script>
</body>
</html>