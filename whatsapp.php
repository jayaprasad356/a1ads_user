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

if (isset($_SESSION['form_success'])) {
    echo '<script>alert("Uploaded successfully.");</script>';
    unset($_SESSION['form_success']); 
}

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT id FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $userData = $db->getResult();
  
}

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT id FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $userData = $db->getResult();

    if (!empty($userData)) {
        $user_id = $userData[0]['id']; 

        $sql_query = "SELECT whatsapp.*, users.name, users.mobile FROM whatsapp LEFT JOIN users ON whatsapp.user_id = users.id WHERE users.mobile = '$mobile'";
        $db->sql($sql_query);
        $res = $db->getResult();
    } else {
        echo 'User not found.';
    }
}

if (isset($_POST['btnAdd'])) {
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $no_of_views = isset($_POST['no_of_views']) ? $db->escapeString($_POST['no_of_views']) : null;

    // Check if the current time is between 9 AM and 6 PM
    $current_hour = date('H');
    if ($current_hour < 9 || $current_hour >= 18) {
        echo '<p class="alert alert-warning">Image upload is allowed only between 9 AM and 6 PM.</p>';
        exit();
    }

    // Check if the user has already uploaded an image for the current day
    $sql_date_check = "SELECT COUNT(*) AS count FROM whatsapp WHERE user_id = '$user_id' AND DATE(datetime) = CURDATE()";
    $db->sql($sql_date_check);
    $res_date_check = $db->getResult();

    if ($res_date_check[0]['count'] > 0) {
        echo '<p class="alert alert-warning">Screenshot already uploaded.</p>';
    } else {
        // Check if an image file is uploaded
        if ($_FILES['image']['size'] != 0 && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
            $result = $fn->validate_image($_FILES["image"]);

            if ($result === true) {
                $extension = pathinfo($_FILES["image"]["name"])['extension'];
                $target_path = 'upload/images';
                $filename = microtime(true) . '.' . strtolower($extension);
                $full_path = $target_path . "/" . $filename;

                // Move the uploaded image to the target path
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $full_path)) {
                    $upload_image = 'upload/images/' . $filename;
                    $current_datetime = date('Y-m-d H:i:s');

                    // Insert the image details into the database
                    $sql = "INSERT INTO whatsapp (image, user_id, no_of_views, datetime)
                            VALUES ('$upload_image', '$user_id', '$no_of_views', '$current_datetime')";
                    $db->sql($sql);

                    $_SESSION['form_success'] = true;
                    header("Location: whatsapp.php");
                    exit();
                } else {
                    echo '<p class="alert alert-danger">Failed to move the uploaded image.</p>';
                }
            } else {
                echo '<p class="alert alert-danger">Invalid image format or size.</p>';
            }
        } else {
            echo '<p class="alert alert-danger">No image uploaded.</p>';
        }
    }
}
$sql_query = "SELECT image FROM whatsapp ORDER BY RAND() LIMIT 1";
$db->sql($sql_query);
$imageData = $db->getResult();

// Check if there is any image data retrieved
if (!empty($imageData)) {
    // Get the image URL
    $imageUrl = 'https://a1ads.site/' . $imageData[0]['image']; // Assuming image URL is retrieved properly
} else {
    // If no image URL is retrieved, set it to empty
    $imageUrl = '';
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
<style>
    .container {
        position: relative;
    }

    #shareButton {
        position: absolute;
        top: -10px !important;
        right: 300px !important;    
    }

    @media (max-width: 768px) {
        #shareButton {
            right: 10px !important; 
        }
        
    }
</style>

<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form class="form-inline" method="GET">
                <div class="form-group mb-3">
                <label for="mobile" class="form-label">Mobile Number</label>
                <button type="button" class="btn btn-danger" id="shareButton">Share</button>
                    
                    <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter your mobile number" required>
                    <!-- Custom validation message -->
                    <div class="invalid-feedback" id="mobileValidationMessage">Mobile number is required.</div>
                </div>
                <button type="submit" class="btn btn-primary" id="viewButton">View</button>
                <button type="button" class="btn btn-success" id="addQueryButton">Add</button>
            </form>
            <div class="card mt-3">
                    <div class="card-body">
                        <?php
                        if ($res) {
                            foreach ($res as $row) {
                                echo '<h5 class="card-title">ID:' . $row['id'] . '</h5>';
                                echo '<p class="card-title">' . $row['name'] . '</p>';
                                echo '<p class="card-text"> ' . $row['mobile'] . '</p>';
                                echo '<p class="card-text">' . getStatusLabel($row['status']) . '</p>';

                             if (isset($row['image'])) {
                               $imagePath = 'https://a1ads.site/'.$row['image'];
                                echo '<p class="card-text">' . '<img src="' . $imagePath . '" alt="Image" width="70" height="70">' . '</p>';
                                    }
    
                               echo '<hr>';
                           }
                        } 
                        function getStatusLabel($status) {
                            // Define status labels based on the status values.
                            $statusLabels = array(
                                '0' => '<span class="text-primary">Processing</span>',
                                '1' => '<span class="text-success">Approved</span>',
                                '2' => '<span class="text-danger">Rejected</span>',
                            );
                        
                            // Check if the status exists in the array, and return the label.
                            if (isset($statusLabels[$status])) {
                                return $statusLabels[$status];
                            } else {
                                return 'Unknown Status';
                            }
                        }
                        
                        ?>
                    </div>
                </div>
        </div>
    </div>

    <div class="modal fade" id="addQueryModal" tabindex="-1" role="dialog" aria-labelledby="addQueryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addQueryModalLabel">Add Query</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeModalButton">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form name="add_query_form" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <div class="col-md-8">
                                <label for="exampleInputFile">Image</label> <i class="text-danger asterik">*</i><?php echo isset($error['image']) ? $error['image'] : ''; ?>
                                <input type="file" name="image" onchange="readURL(this);" accept="image/png, image/jpeg" id="image" required/><br>
                                <img id="blah" src="#" alt="" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="no_of_views">No of Views</label>
                            <input type="number" class="form-control" id="no_of_views" name="no_of_views" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="clearFormButton">Clear</button>
                    <button type="submit" class="btn btn-primary" name="btnAdd">Upload</button>
                </div>
                </form>
            </div>
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
                        if (data.plan === 'A1U') {
                            if (data.whatsapp_status === '1') {
                                openAddQueryModal();
                            } else {
                                // WhatsApp status is not 1, show error message
                                alert('You are not joined in Whatsapp status job');
                                $('#newJoinerMobile').val(mobileNumber);
                                $('#newJoinerMobile').prop('disabled', true);
                            }
                        } else {
                            // Plan is not A1U, show plan-related error message
                            alert('Join Whatsapp status job');
                            $('#newJoinerMobile').val(mobileNumber);
                            $('#newJoinerMobile').prop('disabled', true);
                        }
                    } else {
                        // Status is not 1, show status-related error message
                        alert('User Not-verified');
                        $('#newJoinerMobile').val(mobileNumber);
                        $('#newJoinerMobile').prop('disabled', true);
                    }
                } else {
                    // Mobile number is not registered, display an error message
                    alert('This number is not registered. Please register in the app.');
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



  $('#viewButton').click(function () {
        var mobileNumber = $('#mobile').val();
        if (mobileNumber !== '') {
            $('#mobileValidationMessage').hide();

            // Check if the mobile number is registered
            $.ajax({
                url: 'check_whatsapp.php?mobile=' + mobileNumber,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.registered) {
                        // Mobile number is registered, open the "Add Query" modal
                        openAddQueryModal();
                    } else {
                        // Mobile number is not registered, display an error message
                        alert('Mobile number is not registered.');
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
    $('#image').val(''); // Clear the file input
    $('#no_of_views').val(''); // Clear the number of views input
    $('#blah').attr('src', '').css('display', 'none'); // Clear and hide the image preview
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
<script>
document.getElementById('shareButton').addEventListener('click', function() {
    // Check if Web Share API is supported
    if (navigator.share) {
        // Use Web Share API to share URL
        navigator.share({
            title: 'Title of the shared content',
            text: 'Description of the shared content',
            url: '<?php echo $imageUrl; ?>'
        }).then(() => {
            console.log('Shared successfully');
        }).catch((error) => {
            console.error('Error sharing:', error);
        });
    } else {
        // Fallback: Provide options to share via WhatsApp or other platforms
        var shareUrl = '<?php echo $imageUrl; ?>';
        
        if (shareUrl) {
            // If image URL is not empty, create HTML link for the image
            var imageHTML = "<a data-lightbox='category' href='" + shareUrl + "' data-caption='" + shareUrl + "'><img src='" + shareUrl + "' title='" + shareUrl + "' height='50' /></a>";
        } else {
            // If image URL is empty, display 'No Image'
            var imageHTML = 'No Image';
        }

        // For WhatsApp sharing, you can construct a URL with the content to share
        var whatsappUrl = 'whatsapp://send?text=' + encodeURIComponent(shareUrl);
        // Open WhatsApp sharing in a new window/tab
        window.open(whatsappUrl, '_blank');
        
        // You can add more sharing options here for other platforms if needed
    }
});
</script>

</body>
</html>