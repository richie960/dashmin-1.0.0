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

// Select rows where status is not equal to 1 and complete is not equal to 1, considering NULL values
$sql = "SELECT id, payment_date, MpesaReceiptNumber, firstname, Amount, status, adno, term, last_reminder 
        FROM studentfees 
        WHERE (status <> 1 OR status IS NULL) AND (complete <> 1 OR complete IS NULL)
        ORDER BY payment_date DESC";

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
