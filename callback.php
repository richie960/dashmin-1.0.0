<?php
include 'dbconnection.php';

// Check if content is JSON
header("Content-Type: application/json");

// Read input from POST request
$stkCallbackResponse = file_get_contents('php://input');

// Log the input
$logFile = "Mpesastkresponse.json";
$log = fopen($logFile, "a");
fwrite($log, $stkCallbackResponse);
fclose($log);

// Decode JSON data
$data = json_decode($stkCallbackResponse);

// Check if decoding was successful
if ($data === null || json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400); // Bad Request
    echo json_encode(array("error" => "Invalid JSON data"));
    exit;
}

// Extract data from JSON
$MerchantRequestID = isset($data->Body->stkCallback->MerchantRequestID) ? $data->Body->stkCallback->MerchantRequestID : '';
$CheckoutRequestID = isset($data->Body->stkCallback->CheckoutRequestID) ? $data->Body->stkCallback->CheckoutRequestID : '';
$ResultCode = isset($data->Body->stkCallback->ResultCode) ? $data->Body->stkCallback->ResultCode : '';
$ResultDesc = isset($data->Body->stkCallback->ResultDesc) ? $data->Body->stkCallback->ResultDesc : '';
$Amount = isset($data->Body->stkCallback->CallbackMetadata->Item[0]->Value) ? $data->Body->stkCallback->CallbackMetadata->Item[0]->Value : '';
$TransactionId = isset($data->Body->stkCallback->CallbackMetadata->Item[1]->Value) ? $data->Body->stkCallback->CallbackMetadata->Item[1]->Value : '';
$UserPhoneNumber = isset($data->Body->stkCallback->CallbackMetadata->Item[4]->Value) ? $data->Body->stkCallback->CallbackMetadata->Item[4]->Value : '';

// Prepare and execute SQL query using prepared statements
$stmt = $db->prepare("INSERT INTO studentfees (MerchantRequestID, CheckoutRequestID, ResultCode, Amount, MpesaReceiptNumber, PhoneNumber) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $MerchantRequestID, $CheckoutRequestID, $ResultCode, $Amount, $TransactionId, $UserPhoneNumber);
$stmt->execute();
$stmt->close();

// Close database connection
$db->close();
?>
