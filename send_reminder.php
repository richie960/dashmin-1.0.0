<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1";

// SMS API configuration
$partnerID = '8854'; // Replace with your SMS provider's partner ID
$apikey = '70efa65617bcc559666d74e884c3abb6'; // Replace with your SMS provider's API key
$shortcode = 'Savvy_sms'; // Replace with your SMS provider's shortcode

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receive JSON payload from JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$adno = $data['adno'];
$response = [];

// Query student details from studentfees table
$sqlStudentFees = "SELECT adno, class, term, status, last_reminder
                   FROM studentfees
                   WHERE adno = ?";
$stmtStudentFees = $conn->prepare($sqlStudentFees);
if (!$stmtStudentFees) {
    die("Prepare failed: " . $conn->error);
}
$stmtStudentFees->bind_param("s", $adno);
$stmtStudentFees->execute();
$resultStudentFees = $stmtStudentFees->get_result();

if ($resultStudentFees->num_rows > 0) {
    while ($row = $resultStudentFees->fetch_assoc()) {
        $adno = $row['adno'];
        $class = $row['class'];
        $term = $row['term'];
        $status = $row['status'];
        $lastReminder = $row['last_reminder'];

        $cooldownPeriod = 24 * 60 * 60; // 24 hours in seconds
        $currentTimestamp = time();

        // Check if cooldown period is over or last_reminder is default (0000-00-00 00:00:00)
        if ($status == 0 && ($lastReminder == '0000-00-00 00:00:00' || ($currentTimestamp - strtotime($lastReminder) > $cooldownPeriod))) {

            // Calculate balance for the student
            $balance = calculateBalance($conn, $adno, $class, $term);

            // Get Phonenumber from students table
            $sqlPhonenumber = "SELECT Phonenumber FROM students WHERE adno = ?";
            $stmtPhonenumber = $conn->prepare($sqlPhonenumber);
            if (!$stmtPhonenumber) {
                die("Prepare failed: " . $conn->error);
            }
            $stmtPhonenumber->bind_param("s", $adno);
            $stmtPhonenumber->execute();
            $resultPhonenumber = $stmtPhonenumber->get_result();

            if ($resultPhonenumber->num_rows > 0) {
                $phoneRow = $resultPhonenumber->fetch_assoc();
                $phone = $phoneRow['Phonenumber'];

                // Send SMS reminder
                $message = "Reminder: Please complete your payment for adno: $adno, class: $class, term: $term. Balance: $balance KES.";
                $smsUrl = 'https://sms.savvybulksms.com/api/services/sendsms';
                $smsUrl .= '?partnerID=' . urlencode($partnerID);
                $smsUrl .= '&mobile=' . urlencode($phone);
                $smsUrl .= '&apikey=' . urlencode($apikey);
                $smsUrl .= '&shortcode=' . urlencode($shortcode);
                $smsUrl .= '&message=' . urlencode($message);

                $responseContent = file_get_contents($smsUrl);
                $responseObj = json_decode($responseContent, true);

                // Check if SMS was sent successfully
                if ($responseObj && isset($responseObj['responses'][0]['response-code']) && $responseObj['responses'][0]['response-code'] == 200) {
                    $response[] = ["adno" => $adno, "status" => "success", "balance" => $balance];
                    // Update last reminder timestamp
                    $sqlUpdate = "UPDATE studentfees SET last_reminder = NOW() WHERE adno = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdate);
                    if (!$stmtUpdate) {
                        die("Prepare failed: " . $conn->error);
                    }
                    $stmtUpdate->bind_param("s", $adno);
                    $stmtUpdate->execute();
                    $stmtUpdate->close();
                } else {
                    $response[] = ["adno" => $adno, "status" => "failure", "balance" => $balance];
                }
            } else {
                $response[] = ["adno" => $adno, "status" => "phone_not_found"];
            }

            $stmtPhonenumber->close();
        } else {
            $response[] = ["adno" => $adno, "status" => "cooldown_or_inactive"];
        }
    }
} else {
    $response[] = ["adno" => $adno, "status" => "student_not_found"];
}

$stmtStudentFees->close();

// Encode and send JSON response
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();

// Function to calculate balance for a student in a specific class and term
function calculateBalance($conn, $adno, $class, $term) {
    $balance = 0;

    // Get total paid amount for the student in the specified class and term
    $sqlPaid = "SELECT SUM(Amount) AS totalPaid
                FROM studentfees
                WHERE adno = ? AND class = ? AND term = ?";
    $stmtPaid = $conn->prepare($sqlPaid);
    if (!$stmtPaid) {
        die("Prepare failed: " . $conn->error);
    }
    $stmtPaid->bind_param("sss", $adno, $class, $term);
    $stmtPaid->execute();
    $stmtPaid->bind_result($totalPaid);
    $stmtPaid->fetch();
    $stmtPaid->close();

    // Get term fees for the class and term from respective class table (classone, classtwo, etc.)
    $termColumn = $term;
    $sqlTermFees = "SELECT {$termColumn} FROM {$class}";
    $stmtTermFees = $conn->prepare($sqlTermFees);
    if (!$stmtTermFees) {
        die("Prepare failed: " . $conn->error);
    }
    $stmtTermFees->execute();
    $stmtTermFees->bind_result($termFees);
    $stmtTermFees->fetch();
    $stmtTermFees->close();

    // Calculate balance
    $balance = $termFees - $totalPaid;

    return $balance;
}
?>
