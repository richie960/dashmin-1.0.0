<?php
require_once('TCPDF-main/tcpdf.php'); // Ensure this path is correct for your installation

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

// Function to generate PDF for a student
function generateStudentReport($db, $adno = null) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Restoration Education & Community Development Center');
    $pdf->SetTitle('Student Financial Report');
    $pdf->SetSubject('Financial Report');
    $pdf->SetKeywords('TCPDF, PDF, financial, report, student');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'RESTORATION EDUCATION & COMMUNITY DEVELOPMENT CENTER', 'Student Financial Report', array(0,64,255), array(0,64,128));
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    // Set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // Set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Set font
    $pdf->SetFont('dejavusans', '', 10);

    // Add a page
    $pdf->AddPage();

    // Query to fetch student details
    $sqlStudent = "SELECT * FROM students";
    if ($adno) {
        $sqlStudent .= " WHERE adno = '$adno'";
    }

    $resultStudent = $db->query($sqlStudent);
    if ($resultStudent->num_rows > 0) {
        while ($student = $resultStudent->fetch_assoc()) {
            $adno = $student['adno'];

            // Add student profile image at the top right
            $imagePath = $student['profile_image'];
            $imagePath = str_replace('../', '', $imagePath); // Remove '../' from the path
            if (!empty($imagePath) && file_exists($imagePath)) {
                $pdf->Image($imagePath, 160, 30, 30, 30, '', '', '', false, 300, '', false, false, 1, false, false, false);
            }
            

            // Move to the left of the image for student details
            $pdf->SetXY(20, 30); // Adjust X position for details

            $pdf->SetFont('dejavusans', 'B', 12);
            $pdf->Cell(0, 0, 'Student Details', 0, 1, 'L');
            $pdf->SetFont('dejavusans', '', 10);
            $pdf->Cell(0, 0, "Adno: " . $student['adno'], 0, 1, 'L');
            $pdf->Cell(0, 0, "Name: " . $student['firstname'] . " " . $student['lastname'], 0, 1, 'L');
            $pdf->Cell(0, 0, "Class: " . $student['class'], 0, 1, 'L');
            $pdf->Cell(0, 0, "Phone Number: " . $student['Phonenumber'], 0, 1, 'L');
            $pdf->Ln(10);

            // Query to fetch student transactions
            $sqlTransactions = "SELECT * FROM studentfees WHERE adno = '$adno' AND complete is NULL";
            $resultTransactions = $db->query($sqlTransactions);

            if ($resultTransactions->num_rows > 0) {
                $pdf->SetFont('dejavusans', 'B', 12);
                $pdf->Cell(0, 0, 'Transactions', 0, 1, 'L');
                $pdf->SetFont('dejavusans', '', 10);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetDrawColor(128, 0, 0);
                $pdf->SetLineWidth(0.3);

                // Column headers
                $pdf->Cell(30, 7, 'Class', 1, 0, 'C', 1);
                $pdf->Cell(30, 7, 'Term', 1, 0, 'C', 1);
                $pdf->Cell(30, 7, 'Amount', 1, 0, 'C', 1);
                $pdf->Cell(40, 7, 'Payment Date', 1, 0, 'C', 1);
                $pdf->Ln();

                // Data rows
                $totalAmount = 0;
                while ($transaction = $resultTransactions->fetch_assoc()) {
                    $pdf->Cell(30, 6, $transaction['class'], 1);
                    $pdf->Cell(30, 6, $transaction['term'], 1);
                    $pdf->Cell(30, 6, $transaction['Amount'], 1);
                    $pdf->Cell(40, 6, $transaction['payment_date'], 1);
                    $pdf->Ln();
                    $totalAmount += $transaction['Amount'];
                }

                // Balance
                $balanceMessage = $totalAmount >= 0 ? "Total Paid: " . $totalAmount : "Balance Needed: " . abs($totalAmount);
                $pdf->Cell(0, 10, $balanceMessage, 0, 1, 'R');
            } else {
                $pdf->Cell(0, 0, 'No transactions found.', 0, 1, 'L');
            }

            $pdf->Ln(10);
        }
    } else {
        $pdf->Cell(0, 0, 'No student found.', 0, 1, 'L');
    }

    // Save the PDF to a file
    $fileName = $adno ? "student_report_$adno.pdf" : "student_report_all.pdf";
    $pdf->Output(__DIR__ . "/$fileName", 'F');

    return $fileName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adno = isset($_POST['adno']) ? $_POST['adno'] : null;
    $fileName = generateStudentReport($db, $adno);

    // Provide the PDF as a downloadable file
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
    header('Content-Length: ' . filesize(__DIR__ . "/$fileName"));
    readfile(__DIR__ . "/$fileName");

    // Optionally, delete the file after download
    // unlink(__DIR__ . "/$fileName");
}

$db->close();
?>
