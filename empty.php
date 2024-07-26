<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Specify the column you want to empty
$columnToEmpty = "last_reminder";

// Create the SQL query to set the column to NULL
$sql = "UPDATE studentfees SET $columnToEmpty = NULL";

// Execute the query
if ($conn->query($sql) === TRUE) {
    echo "Column data emptied successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

// Close the connection
$conn->close();
?>
