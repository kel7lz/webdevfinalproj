<?php
session_start();

// Add a test item
$_SESSION['cart'][1] = ($_SESSION['cart'][1] ?? 0) + 1;

echo "<h1>Cart Test</h1>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Cart contents: \n";
print_r($_SESSION['cart'] ?? []);
echo "</pre>";

echo "<p><a href='test_cart.php'>Add Another Item</a></p>";
echo "<p><a href='customer/menu.php'>Go to Menu</a></p>";
echo "<p><a href='customer/cart.php'>Go to Cart</a></p>";