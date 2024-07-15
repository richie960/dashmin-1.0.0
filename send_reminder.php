<?php
$servername = "your_servername";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);
$adno = $data['adno'];

$sql = "SELECT parent_phone FROM student WHERE adno = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $adno);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $parentPhone = $row['parent_phone'];

    // Simulate sending a reminder message (you should replace this with actual message sending code)
    // For example, using an SMS API
    $messageSent = true; // Simulate a successful message sending

    if ($messageSent) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
