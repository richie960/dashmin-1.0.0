<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Path to the file to include
$file = '../studentsc.php';

// Check if the file exists
if (file_exists($file)) {
    include $file;
} else {
    echo "File not found: $file";
}
?>
