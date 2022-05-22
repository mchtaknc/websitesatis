<?php
/*$required = [
    'domain' => 'Bir alan adı girmek zorunludur.',
    'installDomain' => 'Alan adı seçeneklerinden bir tanesinin seçilmesi zorunludur.',
];
requireCheck($required);*/
require_once(__DIR__ . "/../_com/functions.php");
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case "assign-coupon":
            $code = isset($_POST['code']) ? $_POST['code'] : null;
            $query = $db->prepare("select * from coupons where code = ? and status = 'active' and max_usage > 0");
            $query->execute([$code]);
            if(!isset($_SESSION['cart']) && empty($_SESSION['cart'])) {
                $error['type']++;
                $error['coupon'] = false;
            }
            if($query->rowCount() == 0) {
                $error['type']++;
                $error['message'] = "Bu kupon geçerli değildir.";
                $error['coupon'] = false;
                unset($_SESSION['coupon']);
                $_SESSION['cart']['taxTotal'] = $_SESSION['cart']['oldPrice'];
            } else {
                if(isset($_SESSION['coupon']) && $_SESSION['coupon']['code'] == $code) {
                    $error['type']++;
                    $error['message'] = "Kupon zaten kullanımda.";
                    $error['coupon'] = true;
                }
            }
            if($code == '') {
                $error['type']++;
                $error['message'] = "Lütfen bir geçerli bir kod giriniz.";
            }
            if($error['type'] == 0) {
                $codeDetail = $query->fetch();
                $_SESSION['coupon'] = [
                    'code' => $code,
                    'discount' => $codeDetail->value
                ];
                $_SESSION['cart']['taxTotal'] = $_SESSION['cart']['oldPrice'] - $codeDetail->value;
                $db->query("update coupons set max_usage = max_usage - 1 where id = '{$codeDetail->id}'");
                $html = '<li class="list-group-item justify-content-between bg-light coupon-discount d-flex">
                    <div class="text-success">
                        <h5 class="domain-filter-tab-title">Kupon Kodu <span>indiriminiz</span></h5>
                        <small class="code">'.$code.'</small>
                    </div>
                    <span class="text-success discount">- '.number_format($codeDetail->value,2).' TL</span>
                </li>';
                http_response_code(200);
                echo json_encode(['html' => $html]);
            } else {
                http_response_code(400);
                echo json_encode($error);
            }
            break;
        case "add-item":
            $product_id = isset($_POST['product']) ? $_POST['product'] : 0;
            $product = $db->prepare("select * from themes where theme_id = ? limit 1");
            $product->execute([$product_id]);
            $required = [
                'domain' => 'Bir alan adı girmek zorunludur.',
                'installDomain' => 'Alan adı seçeneklerinden bir tanesinin seçilmesi zorunludur.',
            ];
            if ($product->rowCount() == 0) {
                $error['type']++;
            }
            if(isset($_POST['installDomain']) && !in_array($_POST['installDomain'],['owndomain','domainregister','domaintransfer'])) {
                $error['type']++;
            }
            requireCheck($required);
            if($error['type'] == 0) {
                $product = $product->fetch();
                $storedItem = [
                    'qty' => 1,
                    'price' => $product->price,
                    'domain' => [
                        'domain' => $_POST['domain'],
                        'domain_type' => $_POST['installDomain']
                    ],
                    'theme' => $product->name,
                    'item' => json_decode(json_encode($product),1),
                ];
                if (array_key_exists($product->theme_id, $_SESSION['cart']['items'])) {
                    $storedItem = $_SESSION['cart']['items'][$product->theme_id];
                }
                $storedItem['price'] = $product->price * $storedItem['qty'];
                $_SESSION['cart']['items'][$product->theme_id] = $storedItem;
                $total = 0;
                foreach ($_SESSION['cart']['items'] as $value) {
                    $total += $value['price'];
                }
                $_SESSION['cart']['totalPrice'] = $total;
                $_SESSION['cart']['taxPrice'] = $total * 0.18;
                $_SESSION['cart']['taxTotal'] = $total * 1.18;
                $_SESSION['cart']['oldPrice'] = $_SESSION['cart']['totalPrice'] * 1.18;
                if(isset($_SESSION['coupon'])) {
                    $_SESSION['cart']['taxTotal'] -= $_SESSION['coupon']['discount'];
                }
                http_response_code(200);
                echo json_encode('success');
            } else {
                http_response_code(400);
                echo json_encode($error);
            }
            break;
        case "cart-update":
            echo count($_SESSION['cart']['items']);
            break;
        case "getPrice":
            http_response_code(200);
            $taxTotal = $_SESSION['cart']['taxTotal'];
            echo '<span><b>Ara Toplam:</b> '.number_format($_SESSION['cart']['totalPrice'],2) .' TL</span>
            <span><b>KDV:</b> '.number_format($_SESSION['cart']['taxPrice'],2) .' TL</span>
            <span><b>İndirim:</b> '.number_format($_SESSION['cart']['discount'],2) .' TL</span>
            <span><b>Toplam:</b> '.number_format($taxTotal,2) .' TL</span>';
            break;
        case "remove-item":
            $product_id = isset($_POST['product']) ? $_POST['product'] : 0;
            $product = $db->prepare("select * from themes where theme_id = ? limit 1");
            $product->execute([$product_id]);
            if($product->rowCount() == 0) {
                $error['type']++;
            }
            if($error['type'] == 0) {
                $reload = 0;
                if(array_key_exists($product_id,$_SESSION['cart']['items'])) {
                    $_SESSION['cart']['totalPrice'] -= $_SESSION['cart']['items'][$product_id]['price'];
                    $_SESSION['cart']['taxPrice'] = $_SESSION['cart']['totalPrice'] * 0.18;
                    $_SESSION['cart']['taxTotal'] = $_SESSION['cart']['totalPrice'] * 1.18;
                    $_SESSION['cart']['oldPrice'] = $_SESSION['cart']['totalPrice'] * 1.18;
                    unset($_SESSION['cart']['items'][$product_id]);
                    if(empty($_SESSION['cart']['items'])) {
                        unset($_SESSION['cart']);
                    }
                }
                if(empty($_SESSION['cart'])) {
                    $reload = 1;
                }
                http_response_code(200);
                echo json_encode([
                    'message' => 'Seçmiş olduğunuz ürün sepetten kaldırılmıştır.',
                    'reload' => $reload,
                    ]);
            } else {
                http_response_code(400);
                echo json_encode($error);
            }
            break;
        case "whois":
            $whois = new WhoisLib();
            $info = array();
            if (!empty($_POST['domain'])) {
                $domain = $_POST['domain'];
                $domain = trim($domain);
                if (substr(strtolower($domain), 0, 7) == "http://" || substr(strtolower($domain), 0, 8) == "https://") $domain = substr($domain, 8);
                if (substr(strtolower($domain), 0, 4) == "www.") $domain = substr($domain, 4);
                $domain = explode(".", $domain, 2);
                //$info = $whois->search($domain[0], [$domain[1]]);
                $info = $whois->lookup($_POST['domain']);
            }
            http_response_code(200);
            echo json_encode($info);
            break;
    }
}