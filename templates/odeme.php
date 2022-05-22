<?php include_once $path . "templates/headerv3.php"; ?>
<?php
setcookie(session_name(), session_id(), ['samesite' => 'None', 'secure' => false]);
$orderPayment = false;
if(isset($_POST['orderType']) && $_POST['orderType'] == 1 && isset($_POST['order']) && isLogged()) {
    $order = $db->prepare("select * from orders where order_no = ? and customer_id = ?");
    $order->execute([$_POST['order'],$user->customer_id]);
    if($order->rowCount() == 0) {
        redirect($domain);
    }
    $order = $order->fetch();
    $orderPayment = true;
    $_POST['firstname'] = $order->firstname;
    $_POST['lastname'] = $order->lastname;
    $_POST['email'] = $user->email;
    $_POST['phone'] = $order->phone;
    $_POST['address'] = $order->address;
    $_POST['agreement'] = 1;
} else {
    if (!isset($_SESSION['cart'])) {
        redirect($domain);
    }
}
if ($_POST) {
    $required = [
        'firstname' => 'Ad',
        'lastname' => 'Soyad',
        'email' => 'E-Posta',
        'phone' => 'Telefon Numarası',
        'address' => 'Adres',
        'agreement' => 'Kullanım Koşulları ve Mesafeli Satış Sözleşmesi',
        'payment_method' => 'Ödeme Yöntemi'
    ];
    if (!isLogged()) {
        $required['password'] = "Şifre";
        $required['password_confirmation'] = "Şifre (Tekrar)";
    }
    if (!in_array($_POST['payment_method'], ['credit_card', 'bank_transfer'])) {
        $error['type']++;
        $error['message'] = "Lütfen ödeme yöntemi seçiniz.";
    }
    if(!$orderPayment) {
        requireCheck($required);
    }
    if ($error['type'] == 0) {
        $orderNo = 'STD' . time();
        $email = $_POST['email'];
        $payment_amount = $_SESSION['cart']['taxTotal']*100;
        $merchant_oid = $orderNo;
        $user_name = $_POST['firstname'] . ' '.$_POST['lastname'];
        $user_address = $_POST['address'];
        $user_phone = $_POST['phone'];
        if (!isLogged()) {
            $register = $db->prepare("select * from customers where status = 1 and email = ?");
            $register->execute([$_POST['email']]);
            if ($register->rowCount() > 0) {
                $error['type']++;
                $error['message'] = "Bu e-posta adresi sistemimizde kayıtlıdır. Lütfen başka bir e-posta adresiyle tekrar deneyiniz.";
            } else {
                if (!empty($_POST['companyname']) && !empty($_POST['tax_id']) && !empty($_POST['tax_office'])) {
                    $_POST['userType'] = 'corporate';
                } else {
                    $_POST['userType'] = 'individual';
                }
                $hash = hash('sha256',$_POST['password']);
                $db->prepare("insert into customers (
                    type,
                    firstname,
                    lastname,
                    phone,
                    company_name,
                    tax_id,
                    tax_office,
                    address,                       
                    email,
                    password
                ) values (?,?,?,?,?,?,?,?,?,?)")->execute([
                    $_POST['userType'],
                    $_POST['firstname'],
                    $_POST['lastname'],
                    $_POST['phone'],
                    $_POST['companyname'],
                    $_POST['tax_id'],
                    $_POST['tax_office'],
                    $_POST['address'],
                    $_POST['email'],
                    $hash
                ]);
                $user_id = $db->lastInsertId();
                $user = $db->query("select * from customers where customer_id = '{$user_id}'")->fetch();
                $_SESSION['front'] = [
                    'customer_id' => $user->customer_id,
                    'email' => $user->email,
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                ];
            }
        }
        if(isLogged()) {
            $items = array();
            /*$db->prepare("insert into orders (
                customer_id,
                firstname,
                lastname,
                phone,
                company_name,
                tax_id,
                tax_office,
                address,
                order_no,
                subtotal,
                vat,
                total,
                status,
                payment_method,
                payment_details,
                note
            ) values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([
                $user->customer_id,
                $_POST['firstname'],
                $_POST['lastname'],
                $_POST['phone'],
                $_POST['companyname'],
                $_POST['tax_id'],
                $_POST['tax_office'],
                $_POST['address'],
                'STD-' . time(),
                $_SESSION['cart']['totalPrice'],
                $_SESSION['cart']['taxPrice'],
                $_SESSION['cart']['taxTotal'],
                'waiting',
                $_POST['payment_method'],
                null,
                $_POST['notes']
            ]);*/
            if(!$orderPayment) {
                $_SESSION['order'] = [
                    'customer_id' => $user->customer_id,
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'phone' => $_POST['phone'],
                    'companyname' => $_POST['companyname'],
                    'tax_id' => $_POST['tax_id'],
                    'tax_office' => $_POST['tax_office'],
                    'address' => $_POST['address'],
                    'order_no' => $orderNo,
                    'payment_method' => $_POST['payment_method'],
                    'notes' => $_POST['notes']
                ];
                foreach ($_SESSION['cart']['items'] as $item) {
                    /*$items[] = [
                        'id' => 'LMR' . $item['item']['theme_id'],
                        'name' => $item['item']['name'],
                        'category' => 'Website',
                        'price' => $item['price'],
                    ];*/
                    $items[] = array($item['item']['name'], $item['price'], 1);
                }
            } else {
                $payment_amount = $order->total*100;
                foreach (json_decode($order->sessions,1)[0] as $item) {
                    $items[] = array($item['item']['name'], $item['price'], 1);
                }
                $_SESSION['repayment'] = [
                    'orderID' => $order->order_id,
                    'orderNo' => $merchant_oid
                ];
            }
            if ($_POST['payment_method'] == 'credit_card') {
                $merchant_id    = "";
                $merchant_key   = "";
                $merchant_salt  = "";

                $merchant_ok_url = "https://sitedeposu.com/odeme-sonuc?type=success";
                $merchant_fail_url = "https://sitedeposu.com/odeme-sonuc?type=failure";
                $user_basket = base64_encode(json_encode($items));

                if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
                    $ip = $_SERVER["HTTP_CLIENT_IP"];
                } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else {
                    $ip = $_SERVER["REMOTE_ADDR"];
                }
                $user_ip=$ip;
                $timeout_limit = "30";
                $debug_on = 0;
                $test_mode = 0;
                $no_installment	= 0;
                $max_installment = 0;
                $currency = "TL";
                $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$user_basket.$no_installment.$max_installment.$currency.$test_mode;
                $paytr_token=base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
                $post_vals=array(
                    'merchant_id'=>$merchant_id,
                    'user_ip'=>$user_ip,
                    'merchant_oid'=>$merchant_oid,
                    'email'=>$email,
                    'payment_amount'=>$payment_amount,
                    'paytr_token'=>$paytr_token,
                    'user_basket'=>$user_basket,
                    'debug_on'=>$debug_on,
                    'no_installment'=>$no_installment,
                    'max_installment'=>$max_installment,
                    'user_name'=>$user_name,
                    'user_address'=>$user_address,
                    'user_phone'=>$user_phone,
                    'merchant_ok_url'=>$merchant_ok_url,
                    'merchant_fail_url'=>$merchant_fail_url,
                    'timeout_limit'=>$timeout_limit,
                    'currency'=>$currency,
                    'test_mode'=>$test_mode
                );

                $ch=curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1) ;
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
                curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                $result = @curl_exec($ch);
                if(curl_errno($ch))
                    die("PAYTR IFRAME connection error. err:".curl_error($ch));
                curl_close($ch);
                $result=json_decode($result,1);
                if($result['status']=='success')
                    $token=$result['token'];
                else
                    die("PAYTR IFRAME failed. reason:".$result['reason']);
                //iyzico Entegrasyon
                /*$iyzico = new Iyzico();
                $payment = $iyzico->setForm([
                    'conversationId' => rand(1, 100000000),
                    'price' => $_SESSION['cart']['totalPrice'],
                    'paidPrice' => $_SESSION['cart']['taxTotal'],
                    'basketId' => rand(1, 10000),
                    'order_no' => $_SESSION['order']['order_no'],
                    'user' => $_SESSION['front']['customer_id'],
                    'domain' => $domain
                ])
                    ->setBuyer([
                        'id' => $_SESSION['front']['customer_id'],
                        'firstname' => $_POST['firstname'],
                        'lastname' => $_POST['lastname'],
                        'phone' => $_POST['phone'],
                        'email' => $_POST['email'],
                        'identityNumber' => '99999999999',
                        'address' => $_POST['address'],
                        'ip' => '',
                        'city' => "Ankara",
                        'country' => "Türkiye",
                    ])
                    ->setBilling([
                        'contactName' => $_POST['firstname'].' '. $_POST['lastname'],
                        'city' => "Ankara",
                        'country' => "Türkiye",
                        'address' => $_POST['address'],
                    ])
                    ->setItems($items)
                    ->paymentForm();
                $p = $payment->getCheckoutFormContent();
                $pStatus = $payment->getStatus();*/
            }
            if ($_POST['payment_method'] == 'bank_transfer') {
                redirect($domain . 'odeme-sonuc');
            }
        }
    }
}
?>
<section class="padding-100-0">
    <div class="container">
        <div class="succec-domain-search-mesage mb-5">
            <?php echo (isset($token) ? 'ÖDEME' : 'SİPARİŞ BİLGİLERİ') ?>
        </div>
        <form class="checkout-form" method="post" <?php echo (isset($token) ? 'style="display:none"' : null) ?>>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-inner">
                        <div class="row mb-5 align-items-center justify-content-center text-center">
                            <div class="col-md-12 text-left mb-4">
                                <h5>Kişisel Bilgiler</h5>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" placeholder="Ad"
                                           id="firstname" name="firstname"
                                           value="<?php echo($user->firstname !== null ? $user->firstname : $_POST['firstname']) ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-user-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" placeholder="Soyad"
                                           name="lastname"
                                           value="<?php echo($user->lastname !== null ? $user->lastname : $_POST['lastname']) ?>"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control form-control-sm"
                                           placeholder="E-Posta Adresi" name="email"
                                           value="<?php echo($user->email !== null ? $user->email : $_POST['email']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-phone"></i></span>
                                    </div>
                                    <input type="tel" class="form-control form-control-sm phoneNo"
                                           placeholder="Telefon Numarası" name="phone"
                                           value="<?php echo($user->phone !== null ? $user->phone : $_POST['phone']) ?>" required>
                                </div>
                            </div>
                            <?php if (!isLogged()) { ?>
                                <div class="col-md-6 mb-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control form-control-sm" name="password"
                                               placeholder="Şifre" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control form-control-sm"
                                               name="password_confirmation" placeholder="Şifre Tekrar" required>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-left mb-4">
                                <h5>Fatura Adresi</h5>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-file-signature"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" name="companyname"
                                           placeholder="Firma Adı"
                                           value="<?php echo($user->company_name !== null ? $user->company_name : $_POST['companyname']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-building"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" name="tax_office"
                                           placeholder="Vergi Dairesi"
                                           value="<?php echo($user->tax_office !== null ? $user->tax_office : $_POST['tax_office']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fa fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-sm" name="tax_id"
                                           placeholder="Vergi No"
                                           value="<?php echo($user->tax_id !== null ? $user->tax_id : $_POST['tax_id']) ?>"">
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="input-group">
                                    <textarea rows="5" class="form-control form-control-sm" placeholder="Adres"
                                              name="address"
                                              required><?php echo($user->address !== null ? $user->address : $_POST['address']) ?></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 mb-4">
                                <div class="input-group">
                                    <textarea rows="5" class="form-control form-control-sm" name="notes"
                                              placeholder="Siparişinize eklemek istediğiniz bir not veya bildi var ise buraya yazabilirsiniz."><?php echo (isset($_POST['notes']) ? $_POST['notes'] : null) ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <ul class="list-group checkout-price mb-3 search-box-filter-domains">
                        <li class="list-group-item d-flex justify-content-between lh-condensed">
                            <div class="padding-domain-filter">
                                <h5 class="domain-filter-tab-title">SİPARİŞ DETAYI</h5>
                                <div class="form-price-sujjestion-domain totalPrice">
                                    <span><b>Ara Toplam:</b> <?php echo number_format($_SESSION['cart']['totalPrice'], 2) ?> TL</span>
                                    <span><b>%18 KDV:</b> <?php echo number_format($_SESSION['cart']['taxPrice'], 2) ?> TL</span>
                                    <span><b>İndirim:</b> <?php echo number_format($_SESSION['cart']['discount'], 2) ?> TL</span>
                                    <span><b>Toplam:</b> <?php echo number_format($_SESSION['cart']['taxTotal'], 2) ?> TL</span>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="aggrement mb-3">
                        <h5 class="domain-filter-tab-title">ÖDEME YÖNTEMİ</h5>
                        <label><input type="radio" name="payment_method" value="credit_card" checked="checked"> Kredi
                            Kartı / Banka Kartı</label>
                        <label><input type="radio" name="payment_method" value="bank_transfer"> Banka Havalesi /
                            EFT</label>
                        <h5 class="domain-filter-tab-title mt-5">SÖZLEŞME</h5>
                        <label><input type="checkbox" name="agreement" required> Kullanım Koşulları'nı ve Mesafeli Satış
                            Sözleşmesi'ni okudum, onaylıyorum.</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ödeme Yap</button>
                </div>
            </div>
        </form>
        <?php if (isset($token)) { ?>
        <h5 class="text-center">İşleminiz Halen Devam Etmektedir. Lütfen Sayfadan Ayrılmayın.</h5>
        <iframe src="https://www.paytr.com/odeme/guvenli/<?php echo $token;?>" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;display: none"></iframe>
        <?php } ?>
    </div>
</section>
<script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if(isset($token)) { ?>
        iFrameResize({},'#paytriframe');
        $('#paytriframe').fadeIn();
        <?php } ?>
        $('.phoneNo').inputmask("(999) 999-9999");
        <?php if(isset($error) && $error['type'] > 0) { ?>
        alertify.error("<?php echo $error['message'] ?>");
        <?php } ?>
    })
</script>
