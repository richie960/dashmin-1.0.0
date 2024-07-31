<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f0f0f0;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 500px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #333;
}

form {
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 5px;
    color: #333;
}

input, select, button {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
}

button {
    background-color: blue;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: blue;
}

#message {
    text-align: center;
    color: red;
}

        </style>

</head>
<body>
    <div class="container">
        <h1>Student Registration</h1>
        <form id="registrationForm" action="../register_student.php" method="post" enctype="multipart/form-data">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" required><br>

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" required><br>

            <label for="adno">Admission Number:</label>
            <input type="text" id="adno" name="adno" required><br>

            <label for="class">Class:</label>
            <select id="class" name="class" required>
            <option value="classnine">Play_group</option>
            <option value="classseven">PP1</option>
                <option value="classeight">PP2</option>
                
                <option value="classone">Class One</option>
                <option value="classtwo">Class Two</option>
                <option value="classthree">Class Three</option>
                <option value="classfour">Class Four</option>
                <option value="classfive">Class Five</option>
                <option value="classsix">Class Six</option>
                
            </select><br>

            <label for="Phonenumber">Phone Number:</label>
            <input type="text" id="Phonenumber" name="Phonenumber" required><br>

            <label for="profile_image">Profile Image:</label>
            <input type="file" id="profile_image" name="profile_image" required><br>

            <button type="submit">Register</button>
        </form>
        <div id="message"></div>
    </div>
    <script >

document.getElementById('registrationForm').addEventListener('submit', function(event) {
    event.preventDefault();

    let form = event.target;
    let formData = new FormData(form);

    fetch('../register_student.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('message').textContent = data;
    })
    .catch(error => {
        document.getElementById('message').textContent = 'An error occurred: ' + error;
    });
});

    
    </script>
</body>
</html>
