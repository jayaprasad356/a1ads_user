<?php
session_start(); 

// Check if session is not already started
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT COUNT(*) as count, status, plan, whatsapp_status, free_income, id FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $result = $db->getResult();

    if ($result[0]['count'] > 0) {
        $_SESSION['mobile'] = $mobile;

        $_SESSION['user_id'] = $result[0]['id'];

        $response = array(
            'registered' => true,
            'verified' => $result[0]['status'],
            'plan' => $result[0]['plan'],
            'whatsapp_status' => $result[0]['whatsapp_status'],
            'free_income' => $result[0]['free_income']
        );

        if ($result[0]['status'] == 0 && $result[0]['free_income'] == 1) {
            $update_query = "UPDATE users SET payment_verified = 'request' WHERE mobile = '$mobile'";
            $db->sql($update_query);
        }
      
        echo json_encode($response);
    } else {
        echo json_encode(array('registered' => false));
    }
}
?>
