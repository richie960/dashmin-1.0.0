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

// Modify the query to order by status and payment_date
$sql = "SELECT id, payment_date, MpesaReceiptNumber, firstname, Amount, status, adno FROM studentfees ORDER BY status DESC, payment_date DESC";
$result = $conn->query($sql);

$feesData = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feesData[] = $row;
    }
} else {
    echo "0 results";
}
$conn->close();
?>
