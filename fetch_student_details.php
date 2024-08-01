<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$adno = $_POST['adno'];

$response = [];

// Fetch student profile
$sqlProfile = "SELECT adno, firstname, lastname, class, Phonenumber, registration_date, profile_image FROM students WHERE adno = ?";
$stmtProfile = $conn->prepare($sqlProfile);
$stmtProfile->bind_param("s", $adno);
$stmtProfile->execute();
$resultProfile = $stmtProfile->get_result();

if ($resultProfile->num_rows > 0) {
    $response['profile'] = $resultProfile->fetch_assoc();
}

// Fetch student payment history
$sqlFees = "SELECT id,class, term,MpesaReceiptNumber, Amount, payment_date FROM studentfees WHERE adno = ?";
$stmtFees = $conn->prepare($sqlFees);
$stmtFees->bind_param("s", $adno);
$stmtFees->execute();
$resultFees = $stmtFees->get_result();

$fees = [];
$currentTerm = "";
while ($row = $resultFees->fetch_assoc()) {
    $fees[] = $row;
    $currentTerm = $row['term'];
}
$response['fees'] = $fees;
$response['current_term'] = $currentTerm;

echo json_encode($response);

$stmtProfile->close();
$stmtFees->close();
$conn->close();
?>
