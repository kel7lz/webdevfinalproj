<?php
// Show all folders in htdocs
$folders = array_filter(glob('*'), 'is_dir');
echo "<h1>Folders in htdocs:</h1>";
echo "<ul>";
foreach ($folders as $folder) {
    echo "<li><a href='http://localhost/$folder/'>$folder</a></li>";
}
echo "</ul>";