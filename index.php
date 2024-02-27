<?php
session_start();
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");


?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-K8BTDKQT');</script>
<!-- End Google Tag Manager -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css">

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .btn-container {
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K8BTDKQT"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="container mt-5">
        <div class="btn-container">
            <div class="row">
            <a href="https://wa.me/+917339276625?text=Hello Sir,I%20want%20to%20join%20or%20refer%20" class="btn btn-success">I want to Join or Refer Friend</a>

            </div>
            <br>
            <br>
            <!-- <div class="row">
            <a href="query.php" class="btn btn-primary">I have Queries to ask</a>
            </div>
             -->
            
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

</body>
</html>