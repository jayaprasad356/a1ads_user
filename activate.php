<?php
session_start();
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

$errorMessage = ''; // Initialize error message variable

if (isset($_POST['btnAdd'])) {
    $order_id = $db->escapeString($_POST['order_id']);

    if (isset($_SESSION['mobile'])) {
        $mobile = $_SESSION['mobile'];

        $update_query = "UPDATE users SET order_id = '$order_id' WHERE mobile = '$mobile'";
        $db->sql($update_query);
    }

    // Handle image upload
    if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        $result = validate_image($_FILES["image"]); // Assuming you have a function called validate_image

        if ($result === true) {
            $extension = pathinfo($_FILES["image"]["name"])['extension'];
            $target_path = 'upload/images/';
            $filename = microtime(true) . '.' . strtolower($extension);
            $full_path = $target_path . $filename;

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
                $errorMessage = 'Error uploading image.';
            } else {
                $upload_image = 'upload/images/' . $filename;
                $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

                $sql = "INSERT INTO payments (payment_screenshot, user_id ,order_id) VALUES ('$upload_image', '$user_id','$order_id')";
                $db->sql($sql);
            }
        } else {
            $errorMessage = $result;
        }
    } else {
        $errorMessage = 'Image not found or invalid.';
    }
}

// Assuming you have a function called validate_image
function validate_image($image) {
    // Implement your image validation logic here
    // Return true if the image is valid, otherwise return an error message
    // For example, check file type, size, etc.
    return true;
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
                <br>
                <div class="form-group">
                                        <label for="exampleInputFile">Image</label> <i class="text-danger asterik">*</i><?php echo isset($error['payment_screenshot']) ? $error['payment_screenshot'] : ''; ?>
                                        <input type="file" name="image" onchange="readURL(this);" accept="image/png,  image/jpeg" id="payment_screenshot" required/><br>
                                        <img id="blah" src="#" alt="" />
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
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#blah')
                    .attr('src', e.target.result)
                    .width(150)
                    .height(200)
                    .css('display', 'block');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

</body>
</html>