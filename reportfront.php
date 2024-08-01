<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Financial Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: blue;
            color: white;
            border: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generate Student Financial Report</h1>
        <form action="../reports.php" method="post">
            <label for="adno">Admission Number :</label>
            <input type="text" id="adno" name="adno" placeholder="Enter admission number if specific report needed" required>
            <input type="submit" value="Generate Report">
        </form>
    </div>
</body>
</html>
