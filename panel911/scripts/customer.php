<?php
require_once(__DIR__ . "/../../_com/functions.php");
if (!isLogged('admin')) {
    redirect($domain_admin . 'login');
}
$error['type'] = 0;
$error['message'] = "Parametre Hatalı";
if (!staffCheck(session('back.staff_id'))) {
    $error['type']++;
}
if (isset($_POST['action']) && $_POST['action'] == 'list') {
    $array['data'] = array();

    if ($error['type'] == 0) {
        $customers = $db->query("select * from customers where status = 1")->fetchAll();
        $array['data'] = array();
        foreach($customers as $item) {
            $data = array();
            $data[] = $item->firstname;
            $data[] = $item->lastname;
            $data[] = $item->phone;
            $data[] = $item->email;
            $data[] = '
                    <a href="' . $domain_admin . 'customers/edit/' . $item->customer_id . '" class="btn btn-sm btn-clean btn-icon" data-customer="' . $item->customer_id . '" title="Edit"><span class="svg-icon svg-icon-md"><i class="la la-user-edit"></i></span</a>
                    <a href="javascript:;" class="btn btn-sm btn-clean btn-icon removeUser" data-customer="' . $item->customer_id . '" title="Delete"><span class="svg-icon svg-icon-md"><i class="la la-trash"></i></span</a>
                ';
            $array['data'][] = $data;
        }
        echo json_encode($array);
    } else {
        http_response_code(400);
        echo json_encode($array);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $customer_id = $_POST['customer'];
    $required = array(
        'type' => 'Hesap Türü',
        'firstname' => 'Ad',
        'lastname' => 'Soyad',
        'phone' => 'Telefon',
        'email' => 'Email',
    );
    if($_POST['type'] === 'corporate') {
        $required['company'] = "Firma Ünvanı";
        $required['tax_id'] = "Vergi No";
        $required['tax_office'] = "Vergi Dairesi";
    }
    $sorgu = $db->prepare("select * from customers where email = ? and status = 1 and customer_id != ?");
    $sorgu->execute([$_POST['email'],$customer_id]);

    $musteriKontrol = $db->prepare("select * from customers where customer_id = ?");
    $musteriKontrol->execute([$customer_id]);

    if ($musteriKontrol->rowCount() == 0) {
        $error['type']++;
    }

    if ($sorgu->rowCount() > 0) {
        $error['type']++;
        $error['message'] = "Bu email adresiyle daha önce bir müşteri kayıt olmuş. Başka bir email adresiyle tekrar deneyiniz.";
    }

    foreach ($required as $key => $field) {
        if ($_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} boş bırakılamaz.";
        }
    }

    if ($error['type'] == 0) {
        $data = [
            $_POST['type'],
            $_POST['firstname'],
            $_POST['lastname'],
            $_POST['phone'],
            $_POST['email'],
        ];
        $sql = "update customers set
        type = ?,
        firstname = ?,
        lastname = ?,
        phone = ?,
        email = ?";
        if($_POST['type'] == 'corporate') {
            $sql .= ", company_name = ?, tax_id = ?, tax_office = ?";
            $data[] = $_POST['company'];
            $data[] = $_POST['tax_id'];
            $data[] = $_POST['tax_office'];
        }
        if ($_POST['password'] !== '') {
            $password = hash('sha256', $_POST['password']);
            $sql .= ", password = ?";
            $data[] = $password;
        }
        $sql .= " where customer_id = ?";
        $data[] = $customer_id;
        $db->prepare($sql)->execute($data);
        http_response_code(200);
        echo json_encode("Müşteri başarıyla güncellendi.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $customer_id = $_POST['customer'];

    $sorgu = $db->prepare("select * from customers where customer_id = ? and status != 0");
    $sorgu->execute([$customer_id]);
    if ($sorgu->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $db->prepare("update customers set status = 0 where customer_id = ?")->execute([$customer_id]);
        http_response_code(200);
        echo json_encode("Müşteri başarıyla silindi.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}