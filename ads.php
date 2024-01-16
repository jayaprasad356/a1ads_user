<?php
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

include('includes/variables.php');
include_once('includes/custom-functions.php');
include_once('includes/functions.php');
$fn = new custom_functions;
define('API_URL', 'https://colorchallenge.graymatterworks.com/api/ads.php/');

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

curl_close($curl);

if (isset($_GET['id'])) {
    $ID = $db->escapeString($_GET['id']);
    $sql_query = "SELECT * FROM users WHERE id =" . $ID;
    $db->sql($sql_query);
    $res = $db->getResult();

    if (count($res) > 0) {
        $name = $res[0]['name'];
        $mobile = $res[0]['mobile'];
        $refer_code = $res[0]['refer_code'];
  
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

    <style>
   @import url('https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap');

.custom-gradient {
    background: linear-gradient(to right, #ffcc00, #ff9900);
    border-radius: 40px;
    min-width: 200px;
    height: 50px;
}

.custom-img {
    width: 50%;
    max-width: 100px;
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
    background: linear-gradient(to bottom, orange, violet);
}

</style>
</head>


<body>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12" style="display: flex; flex-direction: column; justify-content: flex-start; align-items: center; min-height: 100vh; background-color: darkblue;">
                <div style="display: flex; align-items: center; justify-content: center; padding: 20px; margin-right: 50px;">
                    <img src="https://i.pinimg.com/1200x/b1/88/c6/b188c6801ad1d71d3c962c6e4aa2d0cf.jpg" alt="" style="width: 100px; height: auto; border-radius: 20px;">

                    <div style="margin-left: 20px; color: #ffd700; font-family: 'Archivo Black', sans-serif;">
                        <label style="white-space: nowrap; font-family: 'Archivo Black', sans-serif; font-weight: bold;"><?php echo isset($name) ? $name : ''; ?></label><br>
                        <label style="white-space: nowrap; font-family: 'Archivo Black', sans-serif;"><?php echo isset($mobile) ? $mobile : ''; ?></label><br>
                        <label style="white-space: nowrap; font-family: 'Archivo Black', sans-serif;"><?php echo isset($refer_code) ? $refer_code : ''; ?></label>
                    </div>
                </div>
                <div class="row">
                    <h1 style="font-size: 11.5px; margin-left: 10px; color: #ffd700; font-family: 'Archivo Black', sans-serif; font-weight: bold;">I'm earning money by you watching my advertisement</h1>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="image-container">
                            <img class="card-img-top img-fluid custom-img" src="<?php echo $ads_image; ?>" alt="Advertisement Image">
                            <img class="card-img-top img-fluid custom-img" src="<?php echo $ads_image; ?>" alt="Advertisement Image">
                            <img class="card-img-top img-fluid custom-img" src="<?php echo $ads_image; ?>" alt="Advertisement Image">
                        </div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; ;margin-top:20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35" fill="#ffd700" class="bi bi-easel" viewBox="0 0 16 16">
                        <path d="M8 0a.5.5 0 0 1 .473.337L9.046 2H14a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1.85l1.323 3.837a.5.5 0 1 1-.946.326L11.092 11H8.5v3a.5.5 0 0 1-1 0v-3H4.908l-1.435 4.163a.5.5 0 1 1-.946-.326L3.85 11H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1h4.954L7.527.337A.5.5 0 0 1 8 0M2 3v7h12V3z" />
                    </svg>
                    <h1 style="font-size: 15px;margin-top:10px; margin-left: 16px; color: #ffd700; font-family: 'Archivo Black', sans-serif; font-weight: bold;">Ads Activity Performance</h1>
                </div>
                <div class="row">
                    <div class="card-box">
                        <div style="display: flex; justify-content: space-between;">
                            <div style="text-align: center; margin-right: 10px;">
                                <h6 style="color:white;">Total Earnings</h6>
                                <label style="white-space: nowrap; font-family: 'Archivo Black', sans-serif; color:#07e4e4;">135</label><br>
                            </div>
                            <div style="text-align: center;">
                                <h6 style="color:white;">Total Withdrawals</h6>
                                <label style="white-space: nowrap; font-family: 'Archivo Black', sans-serif; color:#07e4e4;">1234</label><br>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <label style="white-space: nowrap; margin-right: 180px; color: #ffd700;font-family: 'Archivo Black', sans-serif; font-weight: bold;">Withdrawal List</label><br>
                </div>
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
