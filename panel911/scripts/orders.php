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
        $orders = $db->query("select * from orders order by order_id desc")->fetchAll();
        $array['data'] = array();
        foreach($orders as $item) {
            $data = array();
            $customer = $db->prepare("select * from customers where customer_id = ?");
            $customer->execute([$item->customer_id]);
            $customer = $customer->fetch();
            $status = "Ödeme Tamamlandı.";
            $classLabel = "label-success";
            if($item->status == 'waiting') {
                $status = "Ödeme Bekliyor.";
                $classLabel = "label-warning";
            }
            if($item->status == 'return') {
                $status = "İade.";
                $classLabel = "label-danger";
            }
            if($item->status == 'failure') {
                $status = "Ödeme Başarısız.";
                $classLabel = "label-danger";
            }
            $html = '<a href="' . $domain_admin . 'orders/edit/' . $item->order_id . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><span class="svg-icon svg-icon-md"><i class="la la-edit"></i></span></a>';
            $data[] = $item->order_no;
            $data[] = $customer->firstname.' '.$customer->lastname;
            $data[] = number_format($item->total,2);
            $data[] = $item->payment_method == 'bank_transfer' ? 'Banka Havalesi' : 'Kredi Kartı';
            $data[] = '<span class="label label-lg font-weight-bold ' . $classLabel . ' label-inline">' . $status . '</span>';
            $data[] = date('d-m-Y',strtotime($item->order_date));
            $data[] = $html;
            $array['data'][] = $data;
        }
        http_response_code(200);
        echo json_encode($array);
    } else {
        http_response_code(400);
        echo json_encode($array);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $required = array(
        'customer' => 'Müşteri',
        'orderStatus' => 'Durum',
        'order_payment_method' => 'Ödeme Yöntemi',
        'price' => 'Ücret',
        'order_packages' => 'Ürün\Hizmet',
        'order_domain' => 'Alan Adı',
    );
    $order_price = str_replace(',','',$_POST['price']);
    $count = $_POST['order_packages'];
    $customer = $db->prepare("select * from customers where customer_id = ?");
    $customer->execute([$_POST['customer']]);
    foreach($_POST['order_packages'] as $key => $item) {
        if(empty($_POST['order_packages'][$key])) {
            $error['type']++;
            $error['message'] = "Ürün\Hizmet alanı zorunludur.";
            break;
        }
        if(empty($_POST['order_domain'][$key])) {
            $error['type']++;
            $error['message'] = "Alan adı alanı zorunludur.";
            break;
        } else {
            if(!filter_var($_POST['order_domain'][$key],FILTER_VALIDATE_URL)) {
                $error['type']++;
                $error['message'] = "Lütfen geçerli bir domain adresi giriniz.";
                break;
            }
        }
    }
    foreach ($required as $key => $field) {
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} boş bırakılamaz.";
            break;
        }
    }
    if($customer->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $vat = $order_price * 18 / 100;
        $total = $order_price + $vat;
        $db->prepare("insert into orders (customer_id,order_no,subtotal,vat,total,status,payment_method,payment_details,note) values (?,?,?,?,?,?,?,?,?)")->execute([
            $_POST['customer'],
            'STD-' . time(),
            $order_price,
            $vat,
            $total,
            $_POST['orderStatus'],
            $_POST['order_payment_method'],
            null,
            null,
        ]);
        $order_id = $db->lastInsertId();
        foreach ($_POST['order_packages'] as $key => $item) {
            $product = $db->prepare("select * from themes where theme_id = ?");
            $product->execute([$item]);
            $product = $product->fetch();
            $db->prepare("insert into order_products (order_id,item_type,item_id,item_quantity,price,domain) values (?,?,?,?,?,?)")->execute([
                $order_id,
                'theme',
                $item,
                1,
                $product->price,
                $_POST['order_domain'][$key]
            ]);
        }
        echo json_encode('Sipariş başarıyla eklendi.');
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $order_id = isset($_POST['order']) ? $_POST['order'] : 0;
    $required = array(
        'customer' => 'Müşteri',
        'orderStatus' => 'Durum',
        'order_payment_method' => 'Ödeme Yöntemi',
        'price' => 'Ücret'
    );
    $order_price = str_replace(',','',$_POST['price']);
    $order = $db->prepare("select * from orders where order_id = ?");
    $order->execute([$order_id]);
    $customer = $db->prepare("select * from customers where customer_id = ?");
    $customer->execute([$_POST['customer']]);
    foreach ($required as $key => $field) {
        if (!isset($_POST[$key]) || $_POST[$key] == '') {
            $error['type']++;
            $error['message'] = "{$required[$key]} boş bırakılamaz.";
            break;
        }
    }
    if($customer->rowCount() == 0 || $order->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $vat = $order_price * 18 / 100;
        $total = $order_price + $vat;
        $db->prepare("update orders set customer_id = ?, status = ?, vat = ?, subtotal = ?, total = ?, payment_method = ?, note = ? where order_id = ?")->execute([
            $_POST['customer'],
            $_POST['orderStatus'],
            $vat,
            $order_price,
            $total,
            $_POST['order_payment_method'],
            $_POST['note'],
            $order_id
        ]);
        echo json_encode('Sipariş başarıyla güncellendi.');
    } else {
        http_response_code(400);
        echo json_encode($error);
    }
}
if (isset($_POST['action']) && $_POST['action'] === 'getPrice') {
    $item_id = $_POST['item'];
    $itemType = $_POST['itemType'];
    $amount = isset($_POST['amount']) && $_POST['amount'] != '' ? str_replace(',','',$_POST['amount']) : 0;

    $sorgu = $db->prepare("select * from themes where theme_id = ?");
    $sorgu->execute([$item_id]);

    if ($sorgu->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $item = $sorgu->fetch();
        http_response_code(200);
        $price = $amount + $item->price;
        echo json_encode(['price' => $price]);
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}
/*if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $theme_id = $_POST['theme'];
    $sorgu = $db->prepare("select * from themes where theme_id = ?");
    $sorgu->execute([$theme_id]);

    if ($sorgu->rowCount() == 0) {
        $error['type']++;
    }
    if ($error['type'] == 0) {
        $db->prepare("update themes set status = 2 where theme_id = ?")->execute([$theme_id]);
        http_response_code(200);
        echo json_encode("Tema başarıyla silindi.");
    } else {
        http_response_code(404);
        echo json_encode($error);
    }
}*/