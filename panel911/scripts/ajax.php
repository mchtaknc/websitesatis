<?php
require_once(__DIR__ . "/../../_com/functions.php");
if (!isLogged('admin')) {
    redirect($domain_admin . 'login');
}
$error['type'] = 0;
$error['message'] = "Parameter is wrong";
if(!staffCheck(session('back.staff_id'))) {
    $error['type']++;
}
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case "update-information":
            $required = array(
                'name' => 'Firstname and Lastname',
                'email' => 'E-Mail',
            );
            foreach ($required as $key => $require) {
                if ($_POST[$key] == '') {
                    $error['type']++;
                    $error['message'] = "$require is required.";
                }
            }
            if ($error['type'] == 0) {
                $db->prepare("update staff set name = ?, email = ? where staff_id = ?")->execute([$_POST['name'],$_POST['email'],session('back.staff_id')]);
                http_response_code(200);
                echo json_encode("Bilgileriniz başarıyla güncellendi.");
            } else {
                http_response_code(404);
                echo json_encode($error);
            }
            break;
        case "change-password":
            $pass = $_POST['current_pass'];
            $hash = hash('sha256', $pass);
            $newpass = $_POST['new_pass'];
            $newpass_r = $_POST['new_pass_r'];
            $sorgu = $db->prepare("select * from staff where staff_id = ?");
            $sorgu->execute([session('back.staff_id')]);
            $row = $sorgu->fetch();
            if ($newpass !== $newpass_r) {
                $error['type']++;
                $error['message'] = "Şifreleriniz eşleşmiyor.";
            }
            if ($hash !== $row->password) {
                $error['type']++;
                $error['message'] = "Geçerli şifreniz yanlış.";
            }
            if ($pass == '' || $newpass == '' || $newpass_r == '') {
                $error['type']++;
                $error['message'] = "Tüm alanlar zorunludur.";
            }
            if ($sorgu->rowCount() == 0) {
                $error['type']++;
            }
            if ($error['type'] == 0) {
                $newHash = hash('sha256', $newpass);
                $db->prepare("update staff set password = ? where staff_id = ?")->execute([$newHash,session('back.staff_id')]);
                http_response_code(200);
                echo json_encode("Şifreniz başarıyla güncellenmiştir.");
            } else {
                http_response_code(404);
                echo json_encode($error);
            }
            break;
        case "order-update":
            $orderTypes = ['theme_images'];
            if (!in_array($_POST['orderType'], $orderTypes)) {
                $error['type']++;
            }
            if ($error['type'] == 0) {
                foreach ($_POST['orders'] as $key => $item) {
                    if ($_POST['orderType'] == 'theme_images') {
                        $item = str_replace('theme_image-', '', $item);
                        $db->prepare("update theme_images set image_order = ? where id = ?")->execute([$key,$item]);
                    }
                }
            }
            break;
    }
}