<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include('includes/variables.php');
include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$fn = new custom_functions;
/*define('API_URL', 'https://colorchallenge.graymatterworks.com/api/ads.php/');

include_once('includes/crud.php');

$apiUrl = API_URL . "ads.php";

$curl = curl_init($apiUrl);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);

if ($response === false) {
    // Error in cURL request
    echo "Error: " . curl_error($curl);
} else {
    // Successful API response
    $responseData = json_decode($response, true);
    if ($responseData !== null && $responseData["success"]) {
        // Display ad details
        $ads = $responseData["data"];
        if (!empty($ads)) {
            $ads_image = $ads[0]["ads_image"];
        } else {
            echo "No ads found.";
        }
    } else {
        echo "Failed to fetch ads details.";
        if ($responseData !== null) {
            echo " Error message: " . $responseData["message"];
        }
    }
}

curl_close($curl);*/

if (isset($_GET['id'])) {
    $userID = $db->escapeString($_GET['id']);


    $userQuery = "SELECT * FROM users WHERE id = $userID";
    $db->sql($userQuery);
    $userData = $db->getResult();

    if (count($userData) > 0) {
        $name = $userData[0]['name'];
        $mobile = $userData[0]['mobile'];
        $refer_code = $userData[0]['refer_code'];
        $gender = $userData[0]['gender']; 
        $withdrawals = $userData[0]['withdrawals']; 
        $earn = $userData[0]['earn']; 

        $withdrawalQuery = "SELECT * FROM withdrawals WHERE user_id = $userID";
        $db->sql($withdrawalQuery);
        $withdrawalData = $db->getResult();

        function getStatusLabel($status) {
            // Define status labels based on the status values.
            $statusLabels = array(
                '0' => '<span class="text-primary">Processing</span>',
                '1' => '<span class="text-success">paid</span>',
                '2' => '<span class="text-danger">cancelled</span>',
            );

        
            if (isset($statusLabels[$status])) {
                return $statusLabels[$status];
            } else {
                return 'Unknown Status';
            }
        }
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Document</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link href="https://fonts.googleapis.com/css2?family=Goblin+One&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">

    <style>


.custom-gradient {
    background: #f2f2f2;
    border-radius: 40px;
        min-width: 200px;
        height: 50px;

}

.custom-img {
    width: 50%;
    max-width: 80px;
    height: auto;
    margin-bottom: 10px;
}

.image-container {
    display: flex;
    justify-content:center;
    gap: 10px;
    flex-wrap: wrap; 
}



.card-box {
    border: 1px solid #ccc;
    padding: 30px;
    text-align: center;
    width: 330px;
    margin-top: 20px;
    border-radius: 10px;
    background: linear-gradient(to bottom, #d87423, #942156); 
}
.card-body {
    border: 1px solid #ccc;
    padding: 15px;
    width: 330px;
    margin-top: 15px;
    border-radius: 10px;
    background: #c5c8e9;
    display: flex;
    flex-direction: column;
    height: 60px;
    position: relative; 
}
.status {
    color: white;
    font-size: 17px;
    text-align: center;
    font-family: "Lato", sans-serif;
    font-weight: bold;
    position: absolute;
    bottom: 25px;
    left: 80px;
}


.datetime {
    color: black;
    font-size: 10px;
    text-align: center;
    position: absolute;
    top:35px; 
    left: 80px; 
    font-family: "Lato", sans-serif;
   
}

.details {
    color: #cd7e52;
   font-family: "Lato", sans-serif;
    font-weight: bold;
    position: absolute;
    bottom: 0px; 
    right: 10px; 
}
.corner-image {
    position: absolute;
    top: 10px; 
    right: 275px;
    width: 40px; 
    height: 40px; 
}

.row-1 {
    display: flex;
    flex-direction: column;
}


</style>
</head>


<body>
    <div class="container">
        <div class="row">
        <div class="col-12 col-sm-12 custom-gradient" style="display: flex; flex-direction: column; justify-content: flex-start; align-items: center; min-height: 130vh; color: #f8f8f8;">
                <div style="display: flex; align-items: center; justify-content: center; padding: 20px; margin-right: 50px;">
                    <?php
    $imagePath = ($gender == 'male') ? 'images/Group.png' : 'https://static.vecteezy.com/system/resources/previews/010/966/841/original/avatar-girl-cartoon-free-vector.jpg';
    ?>
    <img src="<?php echo $imagePath; ?>" alt="" style="width: 100px; height: auto; border-radius: 20px;">

                    <div style="margin-left: 20px; color:black;">
    <label style="white-space: nowrap;font-family: 'IBM Plex Sans', sans-serif; font-weight: bold;"><?php echo isset($name) ? $name : ''; ?></label><br>
    <?php
$mobile = isset($mobile) ? $mobile : '';

// Display the first two digits, stars, and last two digits
$maskedMobile = substr($mobile, 0, 2) . str_repeat('*', max(0, strlen($mobile) - 4)) . substr($mobile, -2);
?>

<label style="white-space: nowrap; font-family: 'IBM Plex Sans', sans-serif;"><?php echo $maskedMobile; ?></label><br>

    <label style="white-space: nowrap; font-family: 'IBM Plex Sans', sans-serif;"><?php echo isset($refer_code) ? $refer_code : ''; ?></label>
</div>
                </div>
                <div class="row">
                    <h1 style="font-size:10px;margin-right: 80px;color:black;font-family: 'IBM Plex Sans', sans-serif; font-weight: bold;padding:10px;">I'm earning money by you watching my advertisement</h1>
                </div>
                
                <div class="row">
    <div class="col-12">
        <div class="image-container">
            <?php
            $userQuery = "SELECT * FROM product LIMIT 3";
            $db->sql($userQuery);
            $productdata = $db->getResult();

            foreach ($productdata as $product) {
                $imagePath = DOMAIN_URL . $product['image'];

                if (!empty($imagePath)) {
                    $imageHtml = "<img class='card-img-top img-fluid custom-img' src='" . $imagePath . "' alt='Product Image' height='50' />";
                    echo $imageHtml;
                } else {
                    echo 'No Image';
                }
            }
            ?>
        </div>
    </div>
</div>


                <div style="display: flex; align-items: center; margin-top: 20px;">
    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="black" class="bi bi-easel-fill" viewBox="0 0 16 16" style="margin-right: 10px;">
        <path d="M8.473.337a.5.5 0 0 0-.946 0L6.954 2H2a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h1.85l-1.323 3.837a.5.5 0 1 0 .946.326L4.908 11H7.5v2.5a.5.5 0 0 0 1 0V11h2.592l1.435 4.163a.5.5 0 0 0 .946-.326L12.15 11H14a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H9.046z"/>
    </svg>
    <h1 style="font-size:18px; margin-right: 50px; margin-top: 10px; color: black;font-family: 'IBM Plex Sans', sans-serif; font-weight: bold;">Ads Activity Performance</h1>
</div>

                <div class="row">
                    <div class="card-box">
                        <div style="display: flex; justify-content: space-between;">
                            <div style="text-align: center; margin-right: 10px;">
                                <h6 style="color:white;">Total Earnings</h6>
                                <label style="white-space: nowrap; font-family: 'IBM Plex Sans', sans-serif; color:#07e4e4;">₹ <?php echo $earn?></label><br>
                            </div>
                            <div style="text-align: center;">
                                <h6 style="color:white;">Total Withdrawals</h6>
                                <label style="white-space: nowrap; font-family: 'IBM Plex Sans', sans-serif; color:#07e4e4;">₹ <?php echo $withdrawals?></label><br>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
               
<div class="row">
<label style="white-space: nowrap; margin-right: 180px; color: black; font-family: 'IBM Plex Sans', sans-serif; font-weight: bold; text-decoration: underline;">Withdrawal List :</label>
</div>
<div class="row-1">
    <?php
    $withdrawalQuery = "SELECT * FROM withdrawals WHERE user_id = $userID";
    $db->sql($withdrawalQuery);
    $withdrawalData = $db->getResult();

    foreach ($withdrawalData as $withdrawal) {
        ?>
        <div class="card-body">
        <img src="images/transfer.png" alt="Image" class="corner-image">
            <div class="status"><?php echo getStatusLabel($withdrawal['status']); ?></div>
            <div class="datetime"><?php echo $withdrawal['datetime']; ?></div>
            <div class="details">
                <p>₹<?php echo $withdrawal['amount']; ?></p>
                
                <!-- Add more details as needed -->
            </div>
        </div>
        <?php 
    }
    ?>
</div>

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#watchAdsBtn').click(function () {
                // Show loading line
                $('#loadingLine').css('width', '100%');

                // Simulate a delay (replace this with your actual function)
                setTimeout(function () {
                    // Hide loading line
                    $('#loadingLine').css('width', '0');

                    // Refresh the page after 12 seconds
                    setTimeout(function () {
                        location.reload();
                    }, 0);
                }, 12000);
            });
        });
    </script>
</body>

</html>
