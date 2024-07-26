<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

// Create connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to select records from studentfees where firstname or lastname is empty
$sql = "SELECT adno FROM studentfees WHERE firstname IS NULL OR firstname = '' OR lastname IS NULL OR lastname = ''";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Process each record
    while($row = $result->fetch_assoc()) {
        $adno = $row['adno'];
        
        // Query to fetch firstname and lastname from students table using adno
        $sql2 = "SELECT firstname, lastname FROM students WHERE adno = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("s", $adno);
        $stmt2->execute();
        $stmt2->bind_result($firstname, $lastname);
        $stmt2->fetch();
        $stmt2->close();
        
        if ($firstname && $lastname) {
            // Update the studentfees table with fetched firstname and lastname
            $sql3 = "UPDATE studentfees SET firstname = ?, lastname = ? WHERE adno = ?";
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bind_param("sss", $firstname, $lastname, $adno);
            $stmt3->execute();
            $stmt3->close();
        }
    }
} else {
   // echo "No records found with missing firstname or lastname.";
}

// Close connection
$conn->close();
?>
