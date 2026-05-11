<?php
// ============================================================
// includes/database.php — PDO singleton connection
// ============================================================

require_once __DIR__ . '/config.php';

function get_db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                die('A database error occurred. Please try again later.');
            }
        }
    }

    return $pdo;
}

function db_query(string $sql, array $params = []): PDOStatement {
    $stmt = get_db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function db_fetch(string $sql, array $params = []): ?array {
    $row = db_query($sql, $params)->fetch();
    return $row ?: null;
}

function db_fetch_all(string $sql, array $params = []): array {
    return db_query($sql, $params)->fetchAll();
}

function db_insert(string $sql, array $params = []): int {
    db_query($sql, $params);
    return (int) get_db()->lastInsertId();
}

function db_execute(string $sql, array $params = []): int {
    return db_query($sql, $params)->rowCount();
}