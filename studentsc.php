<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <style>
        .profile-image {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border: 1px solid #ccc;
        }
        .profile-details {
            display: flex;
            align-items: center;
        }
        .profile-details img {
            margin-right: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mt-5">Student Management</h1>
    <button class="btn btn-success mb-3" id="upgradeAllButton">Upgrade All Students</button>
    <div class="form-group">
        <label for="classDropdown">Select Class:</label>
        <select class="form-control" id="classDropdown">
        <option value="classnine">Playgroup</option>
        <option value="classseven">PP1</option>
            <option value="classeight">PP2</option>
            <option value="classone">Class 1</option>
            <option value="classtwo">Class 2</option>
            <option value="classthree">Class 3</option>
            <option value="classfour">Class 4</option>
            <option value="classfive">Class 5</option>
            <option value="classsix">Class 6</option>
            
        </select>
    </div>
    <div class="form-group">
        <label for="searchAdno">Search by Adno:</label>
        <input type="text" class="form-control" id="searchAdno" placeholder="Enter Adno">
        <button class="btn btn-primary mt-2" id="searchButton">Search</button>
    </div>
    <table class="table table-striped" id="studentsTable">
        <thead>
            <tr>
                <th>Adno</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Class</th>
                <th>Phone Number</th>
                <th>Registration Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Modal for Viewing and Adding Payment -->
    <div class="modal fade" id="studentModal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentModalLabel">Student Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="profile-details">
                        <img id="profileImage" class="profile-image" alt="Profile Image">
                        <div id="studentProfile">
                            <!-- Student profile will be loaded here -->
                        </div>
                    </div>
                    <h5>Previous Entries</h5>
                    <table class="table table-striped" id="feesTable">
                        <thead>
                            <tr>
                                <th>Class</th>
                                <th>Term</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <h5>Enter New Payment</h5>
                    <form id="newPaymentForm">
                        <div class="form-group">
                            <label for="paymentClass">Class</label>
                            <input type="text" class="form-control" id="paymentClass" readonly>
                        </div>
                        <div class="form-group">
                            <label for="paymentTerm">Term</label>
                            <select class="form-control" id="paymentTerm"></select>
                        </div>
                        <div class="form-group">
                            <label for="paymentAmount">Amount</label>
                            <input type="number" class="form-control" id="paymentAmount">
                        </div>
                        <input type="hidden" id="paymentAdno">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" id="upgradeButton">Upgrade</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editPaymentForm">
                        <div class="form-group">
                            <label for="editPaymentClass">Class</label>
                            <input type="text" class="form-control" id="editPaymentClass" readonly>
                        </div>
                        <div class="form-group">
                            <label for="editPaymentTerm">Term</label>
                            <select class="form-control" id="editPaymentTerm"></select>
                        </div>
                        <div class="form-group">
                            <label for="editPaymentAmount">Amount</label>
                            <input type="number" class="form-control" id="editPaymentAmount">
                        </div>
                        <input type="hidden" id="editPaymentId">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Function to load students for a given class
    function loadStudents(className, adno = '') {
        $.ajax({
            url: '../fetch_students.php',
            type: 'POST',
            data: { class: className, adno: adno },
            dataType: 'json',
            success: function(response) {
                var rows = '';
                $.each(response, function(index, student) {
                    rows += '<tr>';
                    rows += '<td>' + student.adno + '</td>';
                    rows += '<td>' + student.firstname + '</td>';
                    rows += '<td>' + student.lastname + '</td>';
                    rows += '<td>' + student.class + '</td>';
                    rows += '<td>' + student.Phonenumber + '</td>';
                    rows += '<td>' + student.registration_date + '</td>';
                    rows += '<td><button class="btn btn-primary viewProfile" data-adno="' + student.adno + '">View</button></td>';
                    rows += '</tr>';
                });
                $('#studentsTable tbody').html(rows);
            }
        });
    }

    // Function to reload student details and fees table
    function loadStudentDetails(adno) {
        $.ajax({
            url: '../fetch_student_details.php',
            type: 'POST',
            data: { adno: adno },
            dataType: 'json',
            success: function(response) {
                var profileImagePath = response.profile.profile_image;
                $('#profileImage').attr('src',profileImagePath);
                var profile = '<p><strong>Adno:</strong> ' + response.profile.adno + '</p>';
                profile += '<p><strong>First Name:</strong> ' + response.profile.firstname + '</p>';
                profile += '<p><strong>Last Name:</strong> ' + response.profile.lastname + '</p>';
                profile += '<p><strong>Class:</strong> ' + response.profile.class + '</p>';
                profile += '<p><strong>Phone Number:</strong> ' + response.profile.Phonenumber + '</p>';
                profile += '<p><strong>Registration Date:</strong> ' + response.profile.registration_date + '</p>';
                $('#studentProfile').html(profile);

                // Set class and adno for new payment
                $('#paymentClass').val(response.profile.class);
                $('#paymentAdno').val(response.profile.adno);

                var feesRows = '';
                var lastTerm = '';
                $.each(response.fees, function(index, fee) {
                    feesRows += '<tr>';
                    feesRows += '<td>' + fee.class + '</td>';
                    feesRows += '<td>' + fee.term + '</td>';
                    feesRows += '<td>' + fee.Amount + '</td>';
                    feesRows += '<td>' + fee.payment_date + '</td>';
                    feesRows += '<td><button class="btn btn-warning btn-sm editFee" data-id="' + fee.id + '" data-class="' + fee.class + '" data-term="' + fee.term + '" data-amount="' + fee.Amount + '">Edit</button></td>';
                    feesRows += '</tr>';
                    lastTerm = fee.term; // Store the last term
                });
                $('#feesTable tbody').html(feesRows);

                // Populate the term dropdown for new payments
                var termsOptions = '';
                if (lastTerm === 'term1fees') {
                    termsOptions += '<option value="term1fees">Term 1</option>';
                    termsOptions += '<option value="term2fees">Term 2</option>';
                } else if (lastTerm === 'term2fees') {
                    termsOptions += '<option value="term2fees">Term 2</option>';
                    termsOptions += '<option value="term3fees">Term 3</option>';
                } else if (lastTerm === 'term3fees') {
                    termsOptions += '<option value="term1fees">Term 1</option>';
                    termsOptions += '<option value="term2fees">Term 2</option>';
                    termsOptions += '<option value="term3fees">Term 3</option>';

                }
                    else if (lastTerm === '') {
                    termsOptions += '<option value="term1fees">Term 1</option>';
                  
                
                } else {
                    termsOptions += '<option value="term1fees">Term 1</option>';
                }
                $('#paymentTerm').html(termsOptions);
                $('#editPaymentTerm').html(termsOptions);
                $('#studentModal').modal('show');
            }
        });
    }

    // Initialize with default class
    var defaultClass = $('#classDropdown').val();
    loadStudents(defaultClass);

    // Update students table on class selection change
    $('#classDropdown').change(function() {
        var selectedClass = $(this).val();
        loadStudents(selectedClass);
    });

    // Search button click event
    $('#searchButton').click(function() {
        var selectedClass = $('#classDropdown').val();
        var adno = $('#searchAdno').val();
        loadStudents(selectedClass, adno);
    });

    // Handle viewing student profile
    $(document).on('click', '.viewProfile', function() {
        var adno = $(this).data('adno');
        loadStudentDetails(adno);
    });

    // Handle editing fee
    $(document).on('click', '.editFee', function() {
        var id = $(this).data('id');
        var className = $(this).data('class');
        var term = $(this).data('term');
        var amount = $(this).data('amount');

        $('#editPaymentClass').val(className);
        $('#editPaymentTerm').val(term);
        $('#editPaymentAmount').val(amount);
        $('#editPaymentId').val(id);

        $('#editModal').modal('show');
    });

    // Handle form submission for editing payment
    $('#editPaymentForm').submit(function(event) {
        event.preventDefault();
        var id = $('#editPaymentId').val();
        var term = $('#editPaymentTerm').val();
        var amount = $('#editPaymentAmount').val();
        var adno = $('#paymentAdno').val(); // Ensure adno is correctly retrieved

        $.ajax({
            url: '../update_payment.php',
            type: 'POST',
            data: { id: id, term: term, amount: amount },
            success: function(response) {
                $('#editModal').modal('hide');
                $('#studentModal').modal('hide');
                var selectedClass = $('#classDropdown').val();
                loadStudents(selectedClass); // Reload students table
                loadStudentDetails(adno); // Refresh student details to show updated payment info
            },
            error: function(xhr, status, error) {
                console.error("Error updating payment:", error);
            }
        });
    });

    // Handle form submission for new payment
    $('#newPaymentForm').submit(function(event) {
        event.preventDefault();
        var adno = $('#paymentAdno').val(); // Retrieve adno from hidden field
        var className = $('#paymentClass').val(); // Retrieve class from the form field
        var term = $('#paymentTerm').val();
        var amount = $('#paymentAmount').val();

        $.ajax({
            url: '../add_payment.php',
            type: 'POST',
            data: { adno: adno, class: className, term: term, amount: amount },
            success: function(response) {
                $('#studentModal').modal('hide');
                var selectedClass = $('#classDropdown').val();
                loadStudents(selectedClass); // Reload students table
                loadStudentDetails(adno); // Refresh student details to show new payment
            },
            error: function(xhr, status, error) {
                console.error("Error adding payment:", error);
            }
        });
    });

    // Close both modals when edit modal is closed and reload student modal
    $('#editModal').on('hidden.bs.modal', function () {
        var adno = $('#paymentAdno').val();
        $('#studentModal').modal('hide');
        loadStudentDetails(adno); // Refresh student details to show updated payment info
    });

    // Handle upgrade button click in the dashboard
    $('#upgradeAllButton').click(function() {
        var selectedClass = $('#classDropdown').val();
        $.ajax({
            url: '../upgrade_students.php',
            type: 'POST',
            data: {  class: selectedClass },
            success: function(response) {
             
                loadStudents(selectedClass); // Reload students table
            },
            error: function(xhr, status, error) {
                console.error("Error upgrading students:", error);
            }
        });
    });

    // Handle upgrade button click in the modal
    $('#upgradeButton').click(function() {
        var adno = $('#paymentAdno').val(); // Retrieve adno from hidden field
        var selectedClass = $('#classDropdown').val(); // Retrieve selected class
        $.ajax({
            url: '../upgrade_student.php',
            type: 'POST',
            data: { adno: adno, class: selectedClass }, // Send selected class along with adno
            success: function(response) {
                loadStudents(selectedClass); // Reload students table
                loadStudentDetails(adno); // Refresh student details to show updated class
                $('#studentModal').modal('hide');
            },
            error: function(xhr, status, error) {
                console.error("Error upgrading student:", error);
            }
        });
    });
});
</script>
</body>
</html>
