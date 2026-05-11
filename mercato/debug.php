<?php
// Turn on error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Testing includes...</h2>";

// Test 1: Config
echo "Test 1: Loading config... ";
require_once 'includes/config.php';
echo "OK!<br>";

// Test 2: Database
echo "Test 2: Loading database... ";
require_once 'includes/database.php';
echo "OK!<br>";

// Test 3: Functions
echo "Test 3: Loading functions... ";
require_once 'includes/functions.php';
echo "OK!<br>";

// Test 4: Database connection
echo "Test 4: Database connection... ";
$pdo = get_db();
echo "OK!<br>";

// Test 5: Query
echo "Test 5: Testing query... ";
$result = db_fetch("SELECT COUNT(*) AS count FROM categories");
echo "Categories found: " . $result['count'] . "<br>";

echo "<h2>All tests passed!</h2>";