<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve all students
$sql = "SELECT adno, class, term, Amount FROM studentfees";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $adno = $row['adno'];
        $class = $row['class'];
        $term = $row['term'];

        // Calculate total amount for the student
        $totalSql = "SELECT SUM(Amount) as total_amount FROM studentfees WHERE adno = '$adno'";
        $totalResult = $conn->query($totalSql);
        $totalRow = $totalResult->fetch_assoc();
        $totalAmount = $totalRow['total_amount'];

        // Get the corresponding class table amount for the term
        $classTable = strtolower($class); // Convert class name to lowercase to match table names
        $termColumn = strtolower($term); // Convert term name to lowercase to match column names
        $classSql = "SELECT `$termColumn` FROM `$classTable` LIMIT 1"; // Use backticks for table and column names
        $classResult = $conn->query($classSql);

        if ($classResult->num_rows > 0) {
            $classRow = $classResult->fetch_assoc();
            $classAmount = $classRow[$termColumn];

            // Compare amounts and update status
            if ($totalAmount >= $classAmount) {
                $status = 1;
            } else {
                $status = 0;
            }

            // Update the student's status
            $updateSql = "UPDATE studentfees SET status = $status WHERE adno = '$adno'";
            if ($conn->query($updateSql) === TRUE) {
              //  echo "Record updated successfully for adno: $adno<br>";
            } else {
               // echo "Error updating record for adno: $adno - " . $conn->error . "<br>";
            }
        }
    }
} else {
    echo "No records found in student table.";
}

$conn->close();
?>
