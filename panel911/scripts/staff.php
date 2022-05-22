<?php
require_once(__DIR__ . "/../../_com/functions.php");
if (!isLogged('admin')) {
    redirect($domain_admin . 'login');
}
$error['type'] = 0;
$error['message'] = "Parametre Hatalı!";
if (!staffCheck(session('back.staff_id'))) {
    $error['type']++;
}
if (isset($_POST['action']) && $_POST['action'] == 'list') {
    $array['data'] = array();
    if ($error['type'] == 0) {
        $array['data'] = array();
        $staffList = $db->prepare("select * from staff where staff_id != ?");
        $staffList->execute([session('back.staff_id')]);
        foreach($staffList->fetchAll() as $item) {
            $data = array();
            $data[] = $item->name;
            $data[] = $item->email;
            $data[] = '
                    <a href="' . $domain_admin . 'staff/edit/' . $item->staff_id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><span class="svg-icon svg-icon-md"><i class="la la-user-edit"></i></span</a>
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon removeUser" data-id="' . $item->staff_id . '" title="Delete"><span class="svg-icon svg-icon-md"><i class="la la-trash"></i></span</a>
                ';
            $array['data'][] = $data;
        }
        echo json_encode($array);
    } else {
        http_response_code(400);
        echo json_encode($array);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $required = array(
        'name' => 'Name',
        'email' => 'Email',
        'password' => 'Password'
    );

    $sorgu = $db->prepare("select * from staff where email = ?");
    $sorgu->execute([$_POST['email']]);

    if ($sorgu->rowCount() > 0) {
        $error['type']++;
        $error['message'] = "Bu e-posta adresiyle daha önce bir yetkili eklenmiş. Başka bir taneyle tekrar deneyin.";
    }

    foreach ($required as $key => $field) {
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$field} boş bırakılamaz.";
        }
    }

    if ($error['type'] == 0) {
        $password = hash('sha256', $_POST['password']);
        $db->prepare("insert into staff (name,email,password) values (?,?,?)")->execute([$_POST['name'],$_POST['email'],$password]);
        http_response_code(200);
        echo json_encode("The staff has been successfully added.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $staff_id = $_POST['staff'];
    $required = array(
        'name' => 'Name',
        'email' => 'Email'
    );

    $sorgu = $db->prepare("select * from staff where email = ? and staff_id != ?");
    $sorgu->execute([$_POST['email'],$staff_id]);

    $kullaniciKontrol = $db->prepare("select * from staff where staff_id = ?");
    $kullaniciKontrol->execute([$staff_id]);

    if ($kullaniciKontrol->rowCount() == 0) {
        $error['type']++;
    }

    if ($sorgu->rowCount() > 0) {
        $error['type']++;
        $error['message'] = "Bu e-posta adresiyle daha önce bir yetkili eklenmiş. Başka bir taneyle tekrar deneyin.";
    }

    foreach ($required as $key => $field) {
        if ($_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} boş bırakılamaz.";
        }
    }

    if ($error['type'] == 0) {
        $data = [
            $_POST['name'],
            $_POST['email'],
        ];
        $sql = "update staff set name = ?,email = ?";
        if ($_POST['password'] !== '') {
            $password = hash('sha256', $_POST['password']);
            $sql .= ", staff_password = ?";
            $data[] = $password;
        }
        $sql .= " where staff_id = ?";
        $data[] = $staff_id;
        $db->prepare($sql)->execute($data);
        http_response_code(200);
        echo json_encode("Yetkili bilgileri başarıyla güncellendi.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $staff_id = $_POST['staff'];

    $sorgu = $db->prepare("select * from staff where staff_id = ?");
    $sorgu->execute([$staff_id]);

    if ($sorgu->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $db->prepare("delete from staff where staff_id = ?")->execute([$staff_id]);
        http_response_code(200);
        echo json_encode("Yetkili başarıyla silindi.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}