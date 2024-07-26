<?php
// update_payment.php

$id = $_POST['id'];
$term = $_POST['term'];
$amount = $_POST['amount'];

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

$sql = "UPDATE studentfees SET term = ?, amount = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $term, $amount, $id);
$stmt->execute();

//echo "Payment updated successfully";

$stmt->close();
$conn->close();
?>
