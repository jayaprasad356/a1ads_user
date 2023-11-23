<?php
session_start(); 
include_once('includes/crud.php');
$db = new Database();
$db->connect();
$db->sql("SET NAMES 'utf8'");

if (isset($_GET['mobile'])) {
    $mobile = $db->escapeString($_GET['mobile']);

    $sql_query = "SELECT COUNT(*) as count FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $result = $db->getResult();

    if ($result[0]['count'] > 0) {
        $_SESSION['mobile'] = $mobile;

      
    $sql_query = "SELECT id, status FROM users WHERE mobile = '$mobile'";
    $db->sql($sql_query);
    $userData = $db->getResult();

        if (!empty($userData)) {
            $_SESSION['user_id'] = $userData[0]['id'];
        }

        if ($userData[0]['status'] == 0) {
            // Status is 0, send a response indicating this
            $status = 0;
            echo json_encode(array('status' => $status, 'message' => 'User status is 0. Open dialog box.'));
        } else {
            // Status is not 0, you can handle this case as needed
            $status = 1;
            echo json_encode(array('status' => $status, 'message' => 'User verified successfully, you can start work .'));
        }
    } else {
        // User not found
        $status = -1;
        echo json_encode(array('status' => $status, 'message' => 'User not found.'));
    }
}
?>
