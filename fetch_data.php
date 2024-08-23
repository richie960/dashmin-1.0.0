<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "class1"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch student registration data
$sql_students = "SELECT registration_date FROM students";
$result_students = $conn->query($sql_students);

$student_data = array();

if ($result_students->num_rows > 0) {
    while ($row = $result_students->fetch_assoc()) {
        $year = date('Y', strtotime($row["registration_date"]));
        if (!isset($student_data[$year])) {
            $student_data[$year] = 0;
        }
        $student_data[$year]++;
    }
}

// Query to fetch fee payment data
$sql_fees = "SELECT Amount, payment_date FROM studentfees";
$result_fees = $conn->query($sql_fees);

$fee_data = array();

if ($result_fees->num_rows > 0) {
    while ($row = $result_fees->fetch_assoc()) {
        $year = date('Y', strtotime($row["payment_date"]));
        if (!isset($fee_data[$year])) {
            $fee_data[$year] = array("total_fees" => 0, "count" => 0);
        }
        $fee_data[$year]["total_fees"] += $row["Amount"];
        $fee_data[$year]["count"]++;
    }
}

// Combine student and fee data
$data = array();
$cumulative_population = 0;

// Ensure all years from both student and fee data are included
$all_years = array_unique(array_merge(array_keys($student_data), array_keys($fee_data)));
sort($all_years);

foreach ($all_years as $year) {
    $new_students = isset($student_data[$year]) ? $student_data[$year] : 0;
    $cumulative_population += $new_students;

    $average_fees = isset($fee_data[$year]) ? ($fee_data[$year]["total_fees"] / $fee_data[$year]["count"]) : 0;

    $data[$year] = array(
        "year" => $year,
        "fees" => round($average_fees, 2),
        "school" => $cumulative_population
    );
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
