<?php
require_once(__DIR__ . "/../_com/functions.php");
if ($_POST) {
    $required = array(
        'email' => 'E-Posta Adresi',
        'password' => 'Şifre',
    );
    if(checkForm($required)) {
        $error['type']++;
    } else {
        $password = hash('sha256', $_POST['password']);
        $sorgu = $db->prepare("select * from customers where email = ? and status = 1");
        $sorgu->execute([$_POST['email']]);
        if($sorgu->rowCount() == 0) {
            $error['type']++;
            $error['message'] = "E-posta adresi veya şifre yanlış.";
        } else {
            $sonuc = $sorgu->fetch();
            if($sonuc->password != $password) {
                $error['type']++;
                $error['message'] = "E-posta adresi veya şifre yanlış.";
            }
        }
    }
    if ($error['type'] == 0) {
        $_SESSION['front'] = array();
        $token = hash('sha256', uniqid());
        $db->query("update customers set cookie = '{$token}' where customer_id = '{$sonuc->customer_id}'");
        setcookie('customer', $token, strtotime('+7 days'), '/');

        $_SESSION['front']['customer_id'] = $sonuc->customer_id;
        $_SESSION['front']['firstname'] = $sonuc->firstname;
        $_SESSION['front']['lastname'] = $sonuc->lastname;

        http_response_code(200);
        echo json_encode(['message' => 'Başarıyla giriş yaptınız. Yönlendiriliyorsunuz...']);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}