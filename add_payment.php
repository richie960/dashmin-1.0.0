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

$adno = $_POST['adno'];
$class = $_POST['class'];
$current_term = $_POST['term'];
$invoice=$_POST['invoice'];
$new_amount = $_POST['amount'];

// Determine the previous term based on the current term
$previous_term = '';
switch ($current_term) {
    case 'term2fees':
        $previous_term = 'term1fees';
        break;
    case 'term3fees':
        $previous_term = 'term2fees';
        break;
    default:
        $previous_term = '';
        break;
}

// Initialize balance and excess payment
$balance_previous = 0;
$excess_payment_previous = 0;

// Check if there is a previous term to consider
if ($previous_term) {
    // Fetch the total amount paid for the previous term
    $sql = "SELECT SUM(Amount) AS total_paid FROM studentfees WHERE adno = ? AND class = ? AND term = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $adno, $class, $previous_term);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $total_paid_previous = $row['total_paid'];

        // Fetch the total fee for the previous term
        $sql = "SELECT $previous_term FROM $class LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $total_fee_previous = $row[$previous_term];
            
            // Calculate the balance or excess payment
            $net_balance_previous = $total_paid_previous - $total_fee_previous;

            if ($net_balance_previous > 0) {
                // Excess payment from previous term (positive amount)
                $excess_payment_previous = $net_balance_previous;
            } else {
                // Balance from previous term (negative amount)
                $balance_previous = $net_balance_previous;
            }
        }
    }
}

// Check if a balance or excess payment entry has already been processed for the current term
$sql = "SELECT COUNT(*) AS count FROM studentfees WHERE adno = ? AND class = ? AND term = ? AND processed = TRUE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $adno, $class, $current_term);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$processed_exists = $row['count'] > 0;

// Insert the carried-over excess payment if it exists and hasn't been processed yet
if ($excess_payment_previous > 0 && !$processed_exists) {
    $sql = "INSERT INTO studentfees (adno, class, term, Amount, payment_date, processed) VALUES (?, ?, ?, ?, NOW(), TRUE)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $adno, $class, $current_term, $excess_payment_previous);
    $stmt->execute();
}

// Calculate net payment for the current term including any excess from previous term
$net_payment = $new_amount + $excess_payment_previous;

// Fetch the total fee for the current term
$sql = "SELECT $current_term FROM $class LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_fee_current = $row[$current_term];
}

// Determine if the new payment exceeds the total fee for the current term
$excess_payment_current = $net_payment - $total_fee_current;

// Insert the new payment amount for the current term
$checkSql = "SELECT COUNT(*) FROM studentfees WHERE MpesaReceiptNumber = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("s", $invoice);
$checkStmt->execute();
$checkStmt->bind_result($count);
$checkStmt->fetch();
$checkStmt->close();

if ($count > 0) {
    // Invoice number already exists, return an error message
    echo json_encode(["status" => "error", "message" => "Invoice number already exists"]);
} else {
    // Invoice number doesn't exist, proceed with the insertion
   
  

    if ($balance_previous < 0 && !$processed_exists) {
        $sql = "INSERT INTO studentfees (adno, class, term, Amount, payment_date, processed) VALUES (?, ?, ?, ?, NOW(), TRUE)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $adno, $class, $current_term, $balance_previous);
        $stmt->execute();
    }
    
    // Update the 'complete' column to 1 for all rows of the previous term for the specific adno and class
    if ($previous_term) {
        $sql = "UPDATE studentfees SET complete = 1 WHERE adno = ? AND class = ? AND term = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $adno, $class, $previous_term);
        $stmt->execute();
    }


    $sql = "INSERT INTO studentfees (adno, class, term, MpesaReceiptNumber, Amount, payment_date) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $adno, $class, $current_term, $invoice, $new_amount);
    

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Payment added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Error adding payment"]);
    }
    $stmt->close();
}


// Insert excess payment for the current term if applicable


echo json_encode(['success' => true]);

$stmt->close();
$conn->close();
?>
