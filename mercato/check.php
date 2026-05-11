<?php
$path = __DIR__ . '/customer/index.php';
echo "Looking for: " . $path . "<br>";
echo "File exists: " . (file_exists($path) ? "YES" : "NO") . "<br><br>";

echo "Files in customer folder:<br>";
$files = scandir(__DIR__ . '/customer');
foreach($files as $f) {
    if($f != '.' && $f != '..') echo "- $f<br>";
}
?>