<?php
session_start();
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

if (isset($_POST['btnAdd'])) {
    // Assuming 'order_id' is a field in your form
    $order_id = $db->escapeString($_POST['order_id']);

    // Check if 'mobile' is set in the session
    if (isset($_SESSION['mobile'])) {
        $mobile = $_SESSION['mobile'];

        // Update order_id in the users table
        $update_query = "UPDATE users SET order_id = '$order_id' WHERE mobile = '$mobile'";
        $db->sql($update_query);
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form class="form-inline" method="GET">
                <div class="form-group mb-3">
                        <label for="mobileNumber" class="form-label">Enter New Mobile Number</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter your mobile number" required>
                        <!-- Custom validation message -->
                        <div class="invalid-feedback" id="mobileValidationMessage">Mobile number is required.</div>
                    </div>
                    <button type="button" class="btn btn-success" id="addQueryButton">New Joining</button>
                </form>
    </div>
    <div class="modal fade" id="addQueryModal" tabindex="-1" role="dialog" aria-labelledby="addQueryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQueryModalLabel">Add New Joiner</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalButton">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
           
    <div class="card">
        <div class="card-body">
            <form name="add_query_form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="mobile">New Joiner Mobile number:</label>
                    <input type="tel" class="form-control" id="newJoinerMobile" name="mobile" required disabled>
                </div>

                <div class="form-group">
                    <label for="friend_refer_code">Friend Refer Code:</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="friend_refer_code" name="friend_refer_code" required>
                        </div>
                        </div>
                <div class="form-group">
                    <label for="order_id">Order ID:</label>
                    <input type="text" class="form-control" id="order_id" name="order_id" required>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="clearFormButton">Clear</button>
                    <button type="submit" class="btn btn-primary" name="btnAdd">Request Activation</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.min.js"></script>
<script>

$(document).ready(function () {
    // Function to open the "Add Query" modal
    function openAddQueryModal() {
        $('#addQueryModal').modal('show');
    }

    $('#addQueryButton').click(function () {
        // Check if the Mobile Number field is not empty
        var mobileNumber = $('#mobile').val();
        if (mobileNumber !== '') {
            // Check if the mobile number is registered
            $.ajax({
                url: 'check_mobiles.php?mobile=' + mobileNumber,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.registered) {
                        if (data.verified === '1') {
                            alert('User verified successfully, you can start work .');
                        } else {
                            $('#newJoinerMobile').val(mobileNumber);
                            $('#newJoinerMobile').prop('disabled', true);
                            // Mobile number is registered, open the "Add Query" modal
                            openAddQueryModal();
                        }
                    } else {
                        // Mobile number is not registered, display an error message
                        alert('This number is not registered ,Please register in app .');
                    }
                },
                error: function () {
                    // Handle any errors here
                    alert('Error checking mobile number.');
                }
            });
        } else {
            // If mobile number is empty, show validation message
            $('#mobileValidationMessage').show();
        }
    });

    // Function to handle the "View" button click
    $('#viewButton').click(function () {
        var mobileNumber = $('#mobile').val();
        if (mobileNumber !== '') {
            $('#mobileValidationMessage').hide();

            // Check if the mobile number is registered
            $.ajax({
                url: 'check_mobile.php?mobile=' + mobileNumber,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.registered) {
                        // Mobile number is registered, open the "Add Query" modal
                        openAddQueryModal();
                    } else {
                        // Mobile number is not registered, display an error message
                        alert('This number is not registered ,Please register in app .');
                    }
                },
                error: function () {
                    // Handle any errors here
                    alert('Error checking mobile number.');
                }
            });
        } else {
            // If mobile number is empty, show validation message
            $('#mobileValidationMessage').show();
        }
    });

    $('#clearFormButton').click(function () {
        $('#mobile').val('');
        $('#friend_refer_code').val('');
        $('#order_id').val('');
    });

    $('#closeModalButton').click(function () {
        $('#mobile').prop('required', false);
        $('#addQueryModal').modal('hide');
    });

    $('#mobile').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

   
});



</script>

</body>
</html>