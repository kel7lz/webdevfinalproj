<?php
// ============================================================
// King's Cup Coffee — Forgot Password Page
// ============================================================
require_once __DIR__ . '/../includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = auth_handle_forgot_password();
    if (!$error) {
        $success = 'If an account with that email exists, we have sent a password reset link.';
        if (APP_ENV === 'development' && isset($_SESSION['debug_reset_link'])) {
            $success .= '<br><small>Dev mode: <a href="' . $_SESSION['debug_reset_link'] . '">Reset link</a></small>';
            unset($_SESSION['debug_reset_link']);
        }
    }
}

$page_title = 'Forgot Password — ' . APP_NAME;
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
        .auth-logo { text-align: center; margin-bottom: 28px; }
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
            <h1>Forgot Password</h1>
            <p class="subtitle">Enter your email to receive a reset link</p>
        </div>

        <?php if ($error): ?>
            <div class="flash flash-error"><?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="flash flash-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       required autocomplete="email" autofocus>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top: 16px;">
                Send Reset Link
            </button>
        </form>

        <p class="auth-link">
            <a href="<?= APP_URL ?>/customer/login.php">← Back to Login</a>
        </p>
    </div>
</div>
</body>
</html>