<?php
require_once(__DIR__ . "/../../_com/functions.php");

$error['type'] = 0;
$error['message'] = "";

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = $db->prepare("select * from staff where email = ? limit 1");
    $query->execute([$email]);
    $row = $query->fetch();
    if ($query->rowCount() == 0) {
        $error['type']++;
        $error['message'] = "Eposta adresi veya şifre yanlış.";
    } else {
        if ($row->password !== hash('sha256',$password)) {
            $error['type']++;
            $error['message'] = "Eposta adresi veya şifre yanlış.";
        }
    }

    if (empty($email) || empty($password)) {
        $error['type']++;
        $error['message'] = "Tüm alanları doldurunuz.";
    }

    if ($error['type'] == 0) {
        $cookie = hash('sha256', uniqid());

        if (isset($_POST['remember'])) {
            setcookie("staff", $cookie, strtotime('+7 days'), "/");
            $db->prepare("update staff set cookie = ? where staff_id = ?")->execute([$cookie,$row->staff_id]);
        } else {
            setcookie("staff", $cookie, strtotime('-1 days'), "/");
        }

        $_SESSION['back'] = array();
        $_SESSION['back']['staff_id'] = $row->staff_id;
        $_SESSION['back']['name'] = $row->name;
        $_SESSION['back']['email'] = $row->email;

        http_response_code(200);
        echo "Giriş yapıldı. Yönlendiriliyorsunuz...";
    } else {
        http_response_code(404);
        echo $error['message'];
    }
}
