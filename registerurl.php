<?php
// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the access key from the form
    $access_key = $_POST['access_key'];

    // Your existing code
    $registerurl = 'https://api.safaricom.co.ke/mpesa/c2b/v2/registerurl';
    $BusinessShortCode = '4118403'; // Replace with your actual BusinessShortCode
    $confirmationUrl = 'https://kabaritageneralagency.wuaze.com/kabarita/confirmation_url.php';
    $validationUrl = 'https://kabaritageneralagency.wuaze.com/kabarita/validation_url.php';

    // Fetch existing URLs (assuming you have stored them somewhere)
    $existingConfirmationUrl = 'https://kabaritacoltd.000webhostapp.com/kabarita/confirmation_url.php'; // Replace with your actual existing confirmation URL
    $existingValidationUrl = 'https://kabaritacoltd.000webhostapp.com/kabarita/validation_url.php'; // Replace with your actual existing validation URL

    // Check if the new URLs are different from the existing ones
    if ($confirmationUrl == $existingConfirmationUrl && $validationUrl == $existingValidationUrl) {
        // URLs are the same, return a message indicating they are already registered
        echo "URLs are already registered.";
    } else {
        // URLs are different, update registration
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $registerurl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json',
            'Authorization:Bearer ' . $access_key
        ));
        $data = array(
            'ShortCode' => $BusinessShortCode,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $confirmationUrl,
            'ValidationURL' => $validationUrl
        );
        $data_string = json_encode($data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);

        // Output the response
        echo $curl_response;

        // Close cURL session
        curl_close($curl);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Key Form</title>
</head>
<body>

    <h2>Enter Access Key</h2>
    
    <!-- Form to input the access key -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="access_key">Access Key:</label>
        <input type="text" name="access_key" required>
        <br>
        <input type="submit" value="Submit">
    </form>

</body>
</html>
