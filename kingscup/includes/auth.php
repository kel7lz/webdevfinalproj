<?php
// ============================================================
// King's Cup Coffee - Authentication System
// ============================================================

require_once __DIR__ . '/functions.php';

function auth_handle_login(): ?string {
    if (!csrf_verify()) return 'Invalid request. Please try again.';

    $login    = sanitize_string($_POST['username'] ?? $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($login) || empty($password)) {
        return 'Username/email and password are required.';
    }

    // Check if login is email or username
    $user = db_fetch(
        'SELECT * FROM users WHERE email = ? OR username = ?',
        [$login, $login]
    );

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return 'Invalid username/email or password.';
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
    $id = db_insert(
        'INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)',
        [$username, $email, $hash, 'customer']
    );

    session_regenerate_id(true);
    $_SESSION['user_id']   = $id;
    $_SESSION['user_role'] = 'customer';
    $_SESSION['username']  = $username;

    return null;
}

function auth_handle_forgot_password(): ?string {
    if (!csrf_verify()) return 'Invalid request.';

    $email = sanitize_email($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Please enter a valid email address.';
    }

    $user = db_fetch('SELECT * FROM users WHERE email = ?', [$email]);

    if (!$user) {
        // Don't reveal if email exists or not
        return null;
    }

    $token = generate_token();
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    db_execute(
        'UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?',
        [$token, $expires, $user['id']]
    );

    $reset_link = APP_URL . '/customer/reset_password.php?token=' . $token;

    // In production, send email
    if (APP_ENV === 'development') {
        $_SESSION['debug_reset_link'] = $reset_link;
    } else {
        // Send email using mail() or PHPMailer
        $subject = 'Password Reset - ' . APP_NAME;
        $message = "Hello {$user['username']},\n\n";
        $message .= "Click the link below to reset your password:\n";
        $message .= $reset_link . "\n\n";
        $message .= "This link expires in 1 hour.\n";
        $message .= "If you didn't request this, please ignore this email.\n\n";
        $message .= "Best regards,\n" . APP_NAME . " Team";

        mail($email, $subject, $message, "From: " . MAIL_FROM_ADDRESS);
    }

    return null;
}

function auth_handle_reset_password(): ?string {
    if (!csrf_verify()) return 'Invalid request.';

    $token    = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($token) || empty($password)) {
        return 'All fields are required.';
    }
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        return 'Passwords do not match.';
    }

    $user = db_fetch(
        'SELECT * FROM users WHERE reset_token = ? AND reset_token_expires > NOW()',
        [$token]
    );

    if (!$user) {
        return 'Invalid or expired reset token.';
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    db_execute(
        'UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?',
        [$hash, $user['id']]
    );

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