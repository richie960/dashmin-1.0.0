<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

// Create connection
$db = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get the selected class and adno from POST data
$selectedClass = $_POST['class'];
$adno = $_POST['adno'];

// Define the next class mapping
$nextClass = [
    'classnine' => 'classeight',
    'classeight' => 'classseven',
    'classseven' => 'classone',
    'classone' => 'classtwo',
    'classtwo' => 'classthree',
    'classthree' => 'classfour',
    'classfour' => 'classfive',
    'classfive' => 'classsix',
    'classsix' => 'classsix' // For simplicity, classnine remains as classnine
];

$nextClassName = $nextClass[$selectedClass];

// Fetch the fees for term3fees from the selected class table
$sqlFetchFees = "SELECT term3fees FROM $selectedClass LIMIT 1";
$resultFees = $db->query($sqlFetchFees);
$termFees = 0;

if ($resultFees->num_rows > 0) {
    $fee = $resultFees->fetch_assoc();
    $termFees = $fee['term3fees'];
}

// Fetch the student from the selected class with the provided adno
$sqlFetchStudent = "SELECT * FROM students WHERE class = '$selectedClass' AND adno = '$adno'";
$resultStudent = $db->query($sqlFetchStudent);

if ($resultStudent->num_rows > 0) {
    $student = $resultStudent->fetch_assoc();

    // Fetch total paid by the student for term3fees of the selected class
    $sqlFetchTotalPaid = "SELECT SUM(Amount) as totalPaid FROM studentfees WHERE adno = '$adno' AND class = '$selectedClass' AND term = 'term3fees'";
    $resultTotalPaid = $db->query($sqlFetchTotalPaid);
    
    $totalPaid = 0;
    if ($resultTotalPaid->num_rows > 0) {
        $totalPaid = $resultTotalPaid->fetch_assoc()['totalPaid'];
    }

    // Only update the student's class if they have payments for term3fees
    if ($totalPaid > 0) {
        // Calculate balance
        $balance = $totalPaid - $termFees;

        // Update student class
        $sqlUpdateClass = "UPDATE students SET class = '$nextClassName' WHERE adno = '$adno'";
        $db->query($sqlUpdateClass);

        // Insert balance into studentfees for the new class
        $sqlInsertBalance = "INSERT INTO studentfees (adno, class, term, Amount, payment_date) VALUES ('$adno', '$nextClassName', 'term1fees', '$balance', NOW())";
        $db->query($sqlInsertBalance);

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'failure', 'message' => 'Student has not made any payments for term3fees']);
    }
} else {
    echo json_encode(['status' => 'failure', 'message' => 'Student not found']);
}

$db->close();
?>
