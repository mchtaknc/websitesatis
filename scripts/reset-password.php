<?php
require_once(__DIR__ . "/../_com/functions.php");
$error['type'] = 0;
$error['message'] = "Parameter is wrong";
if ($_POST) {
    $required = array(
        'newPassword' => 'New Password',
        'newPasswordR' => 'New Password Repeat',
    );

    $sorgu = mysqli_query($db,"select * from oa_user where reset_password = '{$_POST['code']}' and user_status = 1");
    if($_POST['newPassword'] != $_POST['newPasswordR']) {
        $error['type']++;
        $error['message'] = "Passwords does not match.";
    }
    if(strlen($_POST['newPassword']) < 8) {
        $error['type']++;
        $error['message'] = "Your password must be at least 8 characters.";
    }
    foreach ($required as $key => $field) {
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} is required.";
            break;
        }
    }
    if (mysqli_num_rows($sorgu) == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $user = mysqli_fetch_assoc($sorgu);
        $resetPwd = hash('sha256', uniqid());
        $hash = hash('sha256',$_POST['newPassword']);
        mysqli_query($db,"update oa_user set user_password = '{$hash}', reset_password = '{$resetPwd}' where user_id = '{$user['user_id']}'");
        http_response_code(200);
        echo json_encode(['message' => 'Your password has been successfully updated.']);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}