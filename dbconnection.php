<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

// Create connection
$db = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// Close the connection (optional, can be closed when you're done with it)
$db->close();
?>
