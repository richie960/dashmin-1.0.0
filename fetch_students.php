<?php
// fetch_students.php

$class = $_POST['class'];
$adno = $_POST['adno'] ?? '';

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

$sql = "SELECT * FROM students WHERE class = ? AND adno LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%$adno%";
$stmt->bind_param("ss", $class, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$students = array();
while($row = $result->fetch_assoc()) {
    $students[] = $row;
}

echo json_encode($students);

$stmt->close();
$conn->close();
?>
