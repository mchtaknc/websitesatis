<?php
require_once(__DIR__ . "/../_com/functions.php");
$error['type'] = 0;
$error['message'] = "Parameter is wrong";
if($_POST) {
    $required = array(
        'userType' => 'Hesap Türü',
        'firstname' => 'Ad',
        'lastname' => 'Soyad',
        'phone' => 'Telefon Numarası',
        'email' => 'E-Posta Adresi',
        'password' => "Şifre",
        'password_confirmation' => "Şifre (Tekrar)",
        'companyname' => "Firma Adı",
        'tax_office' => "Vergi Dairesi",
        'tax_id' => "Vergi No",
    );
    $register = $db->prepare("select * from customers where status = 1 and email = ?");
    $register->execute([$_POST['email']]);
    requireCheck($required);
    if($_POST['userType'] == 'individual') {
        unset($required['companyname'],$required['tax_office'],$required['tax_id']);
    }

    if ($register->rowCount() > 0) {
        $error['type']++;
        $error['message'] = "Bu e-posta adresi sistemimizde kayıtlıdır. Lütfen başka bir e-posta adresiyle tekrar deneyiniz.";
    }

    if ($error['type'] == 0) {
        $hash = hash('sha256',$_POST['password']);
        $db->prepare("insert into customers (
            type,
            firstname,
            lastname,
            phone,
            company_name,
            tax_id,
            tax_office,
            email,
            password
        ) values (?,?,?,?,?,?,?,?,?)")->execute([
            $_POST['userType'],
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['phone'],
            $_POST['companyname'],
            $_POST['tax_id'],
            $_POST['tax_office'],
            $_POST['email'],
            $hash
        ]);
        $user_id = $db->lastInsertId();
        $userDetail = $db->query("select * from customers where customer_id = '{$user_id}'")->fetch();

        $_SESSION['front'] = [
            'customer_id' => $userDetail->customer_id,
            'email' => $userDetail->email,
            'firstname' => $userDetail->firstname,
            'lastname' => $userDetail->lastname,
        ];

        http_response_code(200);
        echo json_encode(['message' => 'Kaydınız başarıyla tamamlanmıştır. Yönlendiriliyorsunuz...']);
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}