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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $adno = $_POST['adno'];
    $class = $_POST['class'];
    $Phonenumber = $_POST['Phonenumber'];
    $profile_image = $_FILES['profile_image']['name'];
    $target_dir = "images/students/";
    $imageFileType = strtolower(pathinfo($profile_image, PATHINFO_EXTENSION));
    $renamed_image = $adno . "." . $imageFileType;
    $target_file = $target_dir . $renamed_image;

    // Check if adno already exists
    $checkAdnoQuery = "SELECT * FROM students WHERE adno = ?";
    $stmt = $db->prepare($checkAdnoQuery);
    $stmt->bind_param("s", $adno);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "A student with this admission number already exists.";
    } else {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $imagePath = "../" . $target_file; // Path to store in the database

            // Insert the new student record into the database
            $query = "INSERT INTO students (firstname, lastname, adno, class, Phonenumber, profile_image) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->bind_param("ssssss", $firstname, $lastname, $adno, $class, $Phonenumber, $imagePath);

            if ($stmt->execute()) {
                echo "Student registered successfully.";
            } else {
                echo "Error: " . $query . "<br>" . $db->error;
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $stmt->close();
}

$db->close();
?>
