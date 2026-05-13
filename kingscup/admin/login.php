<?php
// ============================================================
// King's Cup Coffee — Admin Login
// ============================================================
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

session_boot();

// Already logged in as admin?
if (is_logged_in() && is_admin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_string($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        // Check user
        $user = db_fetch(
            'SELECT * FROM users WHERE (username = ? OR email = ?) AND role = ?',
            [$username, $username, 'admin']
        );

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid admin credentials.';
        }
    }
}

$page_title = 'Admin Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — King's Cup</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #3B1F0F;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrap {
            background: #F0EDE8;
            border-radius: 16px;
            padding: 40px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 40px rgba(0,0,0,.35);
        }
        .brand { text-align: center; margin-bottom: 28px; }
        .brand-logo {
            width: 56px; height: 56px;
            background: #C8A96E;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 24px; color: #3B1F0F; font-weight: 700;
            margin: 0 auto 12px;
        }
        .brand h1 { font-family: 'Playfair Display', serif; font-size: 22px; color: #2A1A0A; }
        .brand p { font-size: 13px; color: #A89282; margin-top: 3px; }
        .alert-error {
            background: #FEE2E2; color: #991B1B;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; margin-bottom: 18px;
        }
        .form-group { margin-bottom: 16px; }
        label {
            display: block; font-size: 13px; font-weight: 500;
            color: #6B5744; margin-bottom: 6px;
        }
        input {
            width: 100%; padding: 10px 14px;
            border: 1px solid #E2D9CF; border-radius: 8px;
            font-size: 14px; font-family: 'DM Sans', sans-serif;
            background: #fff; color: #2A1A0A; outline: none;
            transition: border-color .2s;
        }
        input:focus { border-color: #C8A96E; }
        .password-wrap { position: relative; }
        .password-toggle {
            position: absolute; right: 12px; top: 50%;
            transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            font-size: 1.2rem;
        }
        .btn-login {
            width: 100%; padding: 12px;
            background: #3B1F0F; color: #C8A96E;
            border: none; border-radius: 8px;
            font-size: 15px; font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer; margin-top: 8px;
            transition: background .2s;
        }
        .btn-login:hover { background: #C8A96E; color: #3B1F0F; }
    </style>
</head>
<body>

<div class="login-wrap">
    <div class="brand">
        <div class="brand-logo">K</div>
        <h1>King's Cup</h1>
        <p>Admin Dashboard Login</p>
    </div>

    <?php if ($error): ?>
        <div class="alert-error"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="username">Username or Email</label>
            <input type="text" id="username" name="username" 
                   placeholder="Enter username or email" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrap">
                <input type="password" id="password" name="password" 
                       placeholder="Enter password" required>
                <button type="button" class="password-toggle" 
                        onclick="togglePassword()">👁️</button>
            </div>
        </div>

        <button type="submit" class="btn-login">Sign In</button>
    </form>
    
    <p style="text-align:center; margin-top: 20px; font-size: 13px; color: #A89282;">
        <a href="../customer/index.php" style="color: #C8A96E; text-decoration: none;">← Back to Website</a>
    </p>
</div>

<script>
function togglePassword() {
    var pw = document.getElementById('password');
    var btn = document.querySelector('.password-toggle');
    pw.type = pw.type === 'password' ? 'text' : 'password';
    btn.textContent = pw.type === 'password' ? '👁️' : '🙈';
}
</script>
</body>
</html>