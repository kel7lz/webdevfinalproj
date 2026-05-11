<?php
// ============================================================
// includes/auth.php — Authentication action handler
// ============================================================

require_once __DIR__ . '/functions.php';

function auth_handle_login(): ?string {
    if (!csrf_verify()) return 'Invalid request. Please try again.';

    $email    = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        return 'Email and password are required.';
    }

    $user = db_fetch('SELECT * FROM users WHERE email = ?', [$email]);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return 'Invalid email or password.';
    }

    session_regenerate_id(true);
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['username']  = $user['username'];

    return null;
}

function auth_handle_register(): ?string {
    if (!csrf_verify()) return 'Invalid request. Please try again.';

    $username = sanitize_string($_POST['username'] ?? '');
    $email    = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        return 'All fields are required.';
    }
    if (strlen($username) < 3 || strlen($username) > 60) {
        return 'Username must be between 3 and 60 characters.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Please enter a valid email address.';
    }
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        return 'Passwords do not match.';
    }

    if (db_fetch('SELECT id FROM users WHERE email = ?', [$email])) {
        return 'An account with this email already exists.';
    }
    if (db_fetch('SELECT id FROM users WHERE username = ?', [$username])) {
        return 'That username is already taken.';
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $id   = db_insert(
        'INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)',
        [$username, $email, $hash, 'customer']
    );

    session_regenerate_id(true);
    $_SESSION['user_id']   = $id;
    $_SESSION['user_role'] = 'customer';
    $_SESSION['username']  = $username;

    return null;
}

function auth_handle_logout(): void {
    session_boot();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']
        );
    }
    session_destroy();
    redirect(APP_URL . '/customer/login.php');
}