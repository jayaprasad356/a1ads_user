<?php
header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once('../includes/crud.php');
include_once('../includes/functions.php');
$fnc = new functions;
$db = new Database();
$db->connect();



$sql = "SELECT id,joined_date FROM `users` WHERE old_plan = 0 AND plan = 'A1' AND status = 1 AND worked_days = 11 AND total_referrals = 0";
$db->sql($sql);
$res= $db->getResult();
$num = $db->numRows($res);
if ($num >= 1){
    
    foreach ($res as $row) {
        $user_id = $row['id'];
        $joined_date = $row['joined_date']; // Assuming $joined_date is a date strin
        $date = new DateTime($joined_date);
        $date->add(new DateInterval('P10D'));
        $new_date = $date->format('Y-m-d'); // Change the format as needed
        $sql = "SELECT COUNT(*) AS leaves
        FROM `leaves`
        WHERE date BETWEEN '$joined_date' AND '$new_date'";
        $this->db->sql($sql);
        $res = $this->db->getResult();
        $leaves = $res[0]['leaves'];
        $total_days = 10 + $leaves;

        $sql = "SELECT SUM(ads) AS total_ads FROM `transactions` WHERE DATE(datetime) >= '$joined_date' AND DATE(datetime) <= DATE_ADD('$joined_date', INTERVAL $total_days DAY)  AND user_id = $user_id";
        $db->sql($sql);
        $res= $db->getResult();
        $total_ads = ($res[0]['total_ads'] !== null) ? $res[0]['total_ads'] : 0;

        $sql = "UPDATE users SET ads_10th_day = $total_ads WHERE id = $user_id";
        $db->sql($sql);

    

    }
    $response['success'] = true;
    $response['message'] = "updated Successfully";
    print_r(json_encode($response));
}
else{
    $response['success'] = false;
    $response['message'] = "Users Not found";
    print_r(json_encode($response));

}
?>