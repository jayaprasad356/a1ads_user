<?php
session_start(); // Start the session
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");
date_default_timezone_set('Asia/Kolkata');

include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$res = [];
$user_id = NULL;

$fn = new Functions(); // Instantiate the Functions class

function isRequestAllowed() {
    $currentTime = strtotime(date('H:i:s'));
    $startTime = strtotime('09:00:00');
    $endTime = strtotime('18:00:00');

    return ($currentTime >= $startTime && $currentTime <= $endTime);
}

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT id FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $userData = $db->getResult();
  
}

if (isset($_POST['btnAdd'])) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $no_of_views = isset($_POST['no_of_views']) ? $db->escapeString($_POST['no_of_views']) : null;
    if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        $result = $fn->validate_image($_FILES["image"]);

        if ($result === true) {
            $extension = pathinfo($_FILES["image"]["name"])['extension'];
            $target_path = 'upload/images';
            $filename = microtime(true) . '.' . strtolower($extension);
            $full_path = $target_path . "" . $filename;

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
                echo '<p class="alert alert-danger">Can not upload image.</p>';
                return false;
            }

            $upload_image = 'upload/images' . $filename;
            $sql = "INSERT INTO whatsapp (image,user_id,no_of_views) VALUES ('$upload_image','$user_id','$no_of_views')";
            $db->sql($sql);


        } else {
            echo '<p class="alert alert-danger">' . $result . '</p>';
        }
    } else {
        $errorMessage = 'User not found. Query insertion failed.';
    }
}
?>

<!-- The rest of your HTML code -->

<!-- Rest of your HTML code -->

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
                        <label for="mobileNumber" class="form-label">Mobile Number</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter your mobile number" required>
                        <!-- Custom validation message -->
                        <div class="invalid-feedback" id="mobileValidationMessage">Mobile number is required.</div>
                    </div>
                    <button type="button" class="btn btn-success" id="addQueryButton">Add</button>
                </form>
        

    <div class="modal fade" id="addQueryModal" tabindex="-1" role="dialog" aria-labelledby="addQueryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQueryModalLabel" >Add </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalButton" required>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <form name="add_query_form" method="post" enctype="multipart/form-data">

                <div class="form-group">
                                    <div class="col-md-8">
                                        <label for="exampleInputFile">Image</label> <i class="text-danger asterik">*</i><?php echo isset($error['image']) ? $error['image'] : ''; ?>
                                        <input type="file" name="image" onchange="readURL(this);" accept="image/png,  image/jpeg" id="image" required/><br>
                                        <img id="blah" src="#" alt="" />
                                    </div>
                                </div>
                                <div class="form-group">
                            <label for="no_of_views">No of Views</label>
                            <input type="number" class="form-control" id="no_of_views" name="no_of_views" required></input>
                        </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="clearFormButton">Clear</button>
                    <button type="submit" class="btn btn-primary" name="btnAdd">Request</button>
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
                            alert('User verified');
                            openAddQueryModal();
                        } else {
                            alert('User Not-verified');
                            $('#newJoinerMobile').val(mobileNumber);
                            $('#newJoinerMobile').prop('disabled', true);
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


<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/656c9f01bfb79148e599aba3/1hgo4q8fu';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

</body>
</html>