<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Path to the file to include
$file = '../registerfront.php';

// Check if the file exists
if (file_exists($file)) {
    include $file;
} else {
    echo "File not found: $file";
}
?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Back Button Example</title>
    <style>
        /* Style for the back button */
        .back-button {
            position: fixed; /* Fixes the button's position */
            top: 10px; /* Distance from the top of the page */
            right: 10px; /* Distance from the right of the page */
            background-color: #007bff; /* Blue background */
            color: white; /* White text */
            border: none; /* Remove default border */
            border-radius: 12px; /* Smooth edges */
            padding: 10px 20px; /* Padding for button size */
            font-size: 16px; /* Font size */
            cursor: pointer; /* Pointer cursor on hover */
            z-index: 1000; /* Ensure it's above other content */
            transition: background-color 0.3s ease; /* Smooth background color change */
        }

        /* Hover effect for the button */
        .back-button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        /* Optional: Adding a box shadow for better visibility */
        .back-button:focus {
            outline: none; /* Remove default focus outline */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2); /* Add shadow */
        }
    </style>
</head>
<body>

    <!-- Back Button -->
    <button class="back-button" onclick="window.history.back();">
        &larr; Back
    </button>

    <!-- Your other HTML content -->

</body>
</html>