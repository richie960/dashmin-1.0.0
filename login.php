<?php
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Buffer all output to prevent sending any unexpected data
    ob_start();

    // Sanitize input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $db->real_escape_string($_POST['password']);
    $rememberMe = isset($_POST['rememberMe']) ? $_POST['rememberMe'] : '';

    // Query to fetch user details
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password (plain text comparison)
        if ($password == $user['password']) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            // Set session timeout
            if ($rememberMe) {
                // Set a long timeout if "Remember me" is checked
                session_set_cookie_params(3600 * 24 * 30); // 30 days
            } else {
                // Set a short timeout for session
                session_set_cookie_params(3600); // 1 hour
            }

            ob_end_clean(); // Clean the output buffer

            // Send JSON response for successful login
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Login successful']);
            exit;
        } else {
            ob_end_clean(); // Clean the output buffer
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
            exit;
        }
    } else {
        ob_end_clean(); // Clean the output buffer
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'No user found with this email']);
        exit;
    }

    $stmt->close();
    $db->close();
}
?>
