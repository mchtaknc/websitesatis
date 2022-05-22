<?php
require_once(__DIR__ . "/../_com/functions.php");
$error['type'] = 0;
$error['message'] = "Parameter is wrong";
if ($_POST) {
    $required = array(
        'email' => 'E-Mail Address',
    );
    $sorgu = mysqli_fetch_assoc(
        mysqli_query(
            $db,
            "select * from oa_user 
            where user_email = '{$_POST['email']}' limit 1"
        )
    );
    if (empty($sorgu) || (isset($sorgu['user_status']) && $sorgu['user_status'] == 0)) {
        $error['type']++;
        $error['message'] = "Email is incorrect";
    }
    if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
        $error['type']++;
        $error['message'] = "Please enter valid e-mail address";
    }
    foreach ($required as $key => $field) {
        if ($_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} is required.";
            break;
        }
    }
    if ($error['type'] == 0) {
        $resetPwd = hash('sha256', uniqid());
        mysqli_query($db,"update oa_user set reset_password = '{$resetPwd}' where user_id = '{$sorgu['user_id']}'");
        $reset = require_once $path . "../templates/mail-templates/reset-password.php";
        $reset_url = $domain . 'reset-password?code=' . $resetPwd;
        $reset = str_replace('{{reset_url}}', $reset_url, $reset);
        phpMailer($_POST['email'], "Onlineauctioncar.co.uk Reset Password Email", $reset);
        http_response_code(200);
        echo json_encode(['message' => 'Password reset link has been sent to your e-mail address.']);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}