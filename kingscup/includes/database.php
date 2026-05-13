<?php
// ============================================================
// King's Cup Coffee - Database Helper Functions
// ============================================================

require_once __DIR__ . '/config.php';

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