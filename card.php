<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$totalStudents = 0;
$totalAmount = 0;
$totalBalance = 0;
$countStatusOne = 0;

// 1. Count total rows in the students table
$sqlCountStudents = "SELECT COUNT(*) AS total FROM students";
$resultCountStudents = $conn->query($sqlCountStudents);

if ($resultCountStudents->num_rows > 0) {
    $row = $resultCountStudents->fetch_assoc();
    $totalStudents = $row['total'];
}

// 2. Calculate total Amount in studentfees table
$sqlTotalAmount = "SELECT SUM(Amount) AS totalAmount FROM studentfees";
$resultTotalAmount = $conn->query($sqlTotalAmount);

if ($resultTotalAmount->num_rows > 0) {
    $row = $resultTotalAmount->fetch_assoc();
    $totalAmount = $row['totalAmount'];
}

// 3. Calculate total balance for rows with status 0
$sqlBalanceSum = "SELECT adno, class, term FROM studentfees WHERE status !=1 AND complete is NULL";
$resultBalanceSum = $conn->query($sqlBalanceSum);

if ($resultBalanceSum->num_rows > 0) {
    while ($row = $resultBalanceSum->fetch_assoc()) {
        $adno = $row['adno'];
        $class = $row['class'];
        $term = $row['term'];
        
        $balance = calculateBalance($conn, $adno, $class, $term);
        $totalBalance += $balance;
    }
}

// 4. Count rows in studentfees with status equal to 1
$sqlCountStatusOne = "SELECT COUNT(*) AS count FROM studentfees WHERE status = 1 AND complete is NULL";
$resultCountStatusOne = $conn->query($sqlCountStatusOne);

if ($resultCountStatusOne->num_rows > 0) {
    $row = $resultCountStatusOne->fetch_assoc();
    $countStatusOne = $row['count'];
}

// Close the database connection
$conn->close();

// Print the results
//echo "Total number of students: $totalStudents\n";
//echo "Total Amount in studentfees: $totalAmount\n";
//echo "Total balance for rows with status 0: $totalBalance\n";
//echo "Number of rows in studentfees with status 1: $countStatusOne\n";

// Function to calculate balance for a student in a specific class and term
function calculateBalance($conn, $adno, $class, $term) {
    $balance = 0;

    // Get total paid amount for the student in the specified class and term
    $sqlPaid = "SELECT SUM(Amount) AS totalPaid
                FROM studentfees
                WHERE adno = ? AND class = ? AND term = ? AND complete IS NULL";
    $stmtPaid = $conn->prepare($sqlPaid);
    $stmtPaid->bind_param("sss", $adno, $class, $term);
    $stmtPaid->execute();
    $stmtPaid->bind_result($totalPaid);
    $stmtPaid->fetch();
    $stmtPaid->close();

    // Get term fees for the class and term from respective class table (classone, classtwo, etc.)
    $termColumn = $term;
    $sqlTermFees = "SELECT {$termColumn} FROM {$class}";
    $stmtTermFees = $conn->prepare($sqlTermFees);
    $stmtTermFees->execute();
    $stmtTermFees->bind_result($termFees);
    $stmtTermFees->fetch();
    $stmtTermFees->close();

    // Calculate balance
    $balance = $termFees - $totalPaid;

    return $balance;
}
?>
