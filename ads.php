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
    <style>
        .custom-gradient {
            background: linear-gradient(to right, #ffcc00, #ff9900);
            border-radius: 40px;
            min-width: 200px;
            height: 50px;
        }

        .loading-line-container {
            background-color: white;
            border-radius: 5px;
            width: 100%;
            height: 20px;
        }

        .loading-line {
            height: 100%;
            width: 0;
            background: linear-gradient(to right, #ffcc00, #ff9900);
            transition: width 12s linear;
            /* Set the transition duration to 12 seconds */
            margin-top: 10px;
            border-radius: 5px;
        }

        .responsive-image {
            width: 100%;
            max-width: 300px;
            height: auto;
            margin-top: 50px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12 col-sm-12" style="display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; background-color: darkblue;">
                <div style="display: flex; align-items: center; margin-top: 10px; width: 200px; margin-right:70px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" style="color: white;" fill="white" class="bi bi-person-circle" viewBox="0 0 16 16">
                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                        <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1" />
                    </svg>

                    <div style="margin-left: 20px; color: white;">
                        <label style="white-space: nowrap;"><?php echo $name; ?></label><br>
                        <label style="white-space: nowrap;"><?php echo $refer_code; ?></label>
                    </div>
                </div>

                <div class="text-center">
                    <!-- Add the icon and name here -->
                    <img src="<?php echo $ads_image; ?>" alt="" class="responsive-image">
                    <p class="label-dark-bold" style="color: white;">Click here to purchase</p>
                    <button id="watchAdsBtn" class="btn label-dark-bold custom-gradient text-white">Watch Ads</button>
                    <br>
                    <div class="loading-line-container">
                        <div id="loadingLine" class="loading-line"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.0/dist/umd/popper.min.js"></script>
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
