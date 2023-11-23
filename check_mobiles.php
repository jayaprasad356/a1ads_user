<?php
session_start(); 
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT COUNT(*) as count, status FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $result = $db->getResult();

    if ($result[0]['count'] > 0) {
        $_SESSION['mobile'] = $mobile;

        $sql_query = "SELECT id FROM users WHERE mobile = '$mobile'";
        $db->sql($sql_query);
        $userData = $db->getResult();

        if (!empty($userData)) {
            $_SESSION['user_id'] = $userData[0]['id'];
        }

        $response = array('registered' => true, 'verified' => $result[0]['status']);

        if ($result[0]['status'] == 0) {
            $update_query = "UPDATE users SET payment_verified = 'request' WHERE mobile = '$mobile'";
            $db->sql($update_query);
        }

        echo json_encode($response);
    } else {
        echo json_encode(array('registered' => false));
    }
}
?>
